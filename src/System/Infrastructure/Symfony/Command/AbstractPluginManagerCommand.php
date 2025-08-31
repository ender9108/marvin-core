<?php

namespace App\System\Infrastructure\Symfony\Command;

use App\Domotic\Domain\Model\Protocol;
use App\Domotic\Domain\Model\ProtocolStatus;
use App\Domotic\Domain\Repository\ProtocolRepositoryInterface;
use App\System\Application\Command\Docker\InstallDockerRequestCommand;
use App\System\Domain\Model\Plugin;
use App\System\Domain\Model\PluginStatus;
use App\System\Domain\Model\User;
use App\System\Domain\Model\UserType;
use App\System\Domain\Repository\PluginRepositoryInterface;
use App\System\Infrastructure\Symfony\Security\SecurityUser;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Yaml\Yaml;

abstract class AbstractPluginManagerCommand extends Command
{
    private const string TYPE_INSTALL = 'install';
    private const string TYPE_UNINSTALL = 'uninstall';
    private const string TYPE_UPDATE = 'update';
    private const array METHODS_AVAILABLES_BY_TYPE = [
        self::TYPE_INSTALL => [
            'App\\System\\Infrastructure\\Symfony\\Command\\AbstractPluginManagerCommand::registerPlugin',
            'App\\System\\Infrastructure\\Symfony\\Command\\AbstractPluginManagerCommand::registerProtocol',
            'App\\System\\Infrastructure\\Symfony\\Command\\AbstractPluginManagerCommand::registerDocker',
            'App\\System\\Infrastructure\\Symfony\\Command\\AbstractPluginManagerCommand::registerSupervisorWorker',
        ],
        self::TYPE_UNINSTALL => [
            'App\\System\\Infrastructure\\Symfony\\Command\\AbstractPluginManagerCommand::unregisterPlugin',
            'App\\System\\Infrastructure\\Symfony\\Command\\AbstractPluginManagerCommand::registerProtocol',
            'App\\System\\Infrastructure\\Symfony\\Command\\AbstractPluginManagerCommand::unregisterDocker',
            'App\\System\\Infrastructure\\Symfony\\Command\\AbstractPluginManagerCommand::unregisterSupervisorWorker',
        ],
        self::TYPE_UPDATE => [
            'App\\System\\Infrastructure\\Symfony\\Command\\AbstractPluginManagerCommand::registerPlugin',
            'App\\System\\Infrastructure\\Symfony\\Command\\AbstractPluginManagerCommand::registerProtocol',
            'App\\System\\Infrastructure\\Symfony\\Command\\AbstractPluginManagerCommand::registerDocker',
            'App\\System\\Infrastructure\\Symfony\\Command\\AbstractPluginManagerCommand::registerSupervisorWorker',
        ],
    ];

    protected ?SymfonyStyle $io = null;

    protected ?Plugin $plugin = null;

    private array $actions = [
        'record_plugin' => false,
        'create_users' => [],
        'docker' => [
            'directories' => [],
            'compose_files' => [],
            'compose_config' => [],
            'custom_commands' => [],
        ],
        'supervisor' => []
    ];

    private FileSystem $fileSystem;

    private array $rollbackActions = [
        'directories' => [],
        'files' => [],
        'compose_services' => []
    ];

    protected ?string $reference = null;
    private bool $force = false;
    private bool $isSecureMode = false;
    private ?string $type = null;
    private bool $dryRun = false;

    private ?string $pluginVersion = null;

    public function __construct(
        protected readonly EntityManagerInterface $em,
        protected readonly ParameterBagInterface $parameters,
        private readonly PluginRepositoryInterface $pluginRepository,
        private readonly ProtocolRepositoryInterface $protocolRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly MessageBusInterface $bus
    ) {
        parent::__construct();

        $this->fileSystem = new Filesystem();
    }

    protected function configure(): void
    {
        $this->addOption('force', 'f', InputOption::VALUE_OPTIONAL, 'Force reinstallation of the plugin', 0);
        $this->addOption('dry-run', 'd', InputOption::VALUE_NONE, 'Used to test the installation');
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     */
    protected function startInstall(
        Callable $callback,
        InputInterface $input,
        OutputInterface $output
    ): void
    {
        $this->type = self::TYPE_INSTALL;
        $this->dryRun = $input->getOption('dry-run');

        $this->secureAction($callback, $input, $output);
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     */
    protected function startUninstall(
        Callable $callback,
        InputInterface $input,
        OutputInterface $output
    ): void
    {
        $this->type = self::TYPE_UNINSTALL;
        $this->dryRun = $input->getOption('dry-run');

        $this->secureAction($callback, $input, $output);
    }

    /**
     * @throws Exception
     * @throws ExceptionInterface
     */
    protected function startUpdate(
        Callable $callback,
        InputInterface $input,
        OutputInterface $output
    ): void
    {
        $this->type = self::TYPE_UPDATE;
        $this->dryRun = $input->getOption('dry-run');

        $this->secureAction($callback, $input, $output);
    }

    /**
     * @throws Exception
     * @todo [LOW] Manage requirements version
     */
    protected function checkPluginRequirement(): void
    {
        $requirements = $this->getPluginRequirements();
        $errors = [];

        foreach ($requirements as $requirementReference) {
            $exists = $this->pluginRepository->isEnabled($requirementReference);

            if (false === $exists) {
                $errors[] = 'Requirement "'.$requirementReference.'" is missing';
            }
        }

        if (!empty($errors)) {
            throw new Exception(implode("\n", $errors));
        }

        $this->io->success('Requirements are ok !');
    }

    /**
     * @throws Exception
     */
    protected function registerPlugin(
        string $label,
        bool $enabled = true,
        ?string $description = null,
    ): void {
        $this->checkIsSecureMode();
        $this->checkTypeMode(__METHOD__);

        $reference = $this->getPluginReference();
        $version = $this->getPluginVersion();
        $plugin = $this->pluginRepository->getByReference($reference);

        if ($plugin instanceof Plugin && false === $this->force) {
            throw new Exception('The plugin "'.$reference.'" already exists');
        }

        if ($plugin instanceof Plugin && true === $this->force) {
            $this->pluginRepository->remove($plugin);
            unset($plugin);
        }

        $pluginStatus = $this
            ->em
            ->getRepository(PluginStatus::class)
            ->findOneBy(['reference' => $enabled ? PluginStatus::STATUS_ENABLED : PluginStatus::STATUS_DISABLED])
        ;

        $plugin = new Plugin();
        $plugin
            ->setLabel($label)
            ->setReference($reference)
            ->setVersion($version)
            ->setDescription($description)
            ->setStatus($pluginStatus)
        ;
        $this->em->persist($plugin);
    }

    /**
     * @throws Exception
     */
    protected function registerProtocol(
        string $label,
        string $reference,
        bool $enabled = true,
        ?string $description = null,
    ): void {
        $this->checkIsSecureMode();
        $this->checkTypeMode(__METHOD__);

        $protocol = $this->protocolRepository->getByReference($reference);

        if ($protocol instanceof Protocol && false === $this->force) {
            throw new Exception('The protocol "'.$reference.'" already exists');
        }

        if ($protocol instanceof Protocol && true === $this->force) {
            $this->protocolRepository->remove($protocol);
            unset($plugin);
        }

        $protocolStatus = $this
            ->em
            ->getRepository(ProtocolStatus::class)
            ->findOneBy(['reference' => $enabled ? ProtocolStatus::STATUS_ENABLED : ProtocolStatus::STATUS_DISABLED])
        ;

        $protocol = new Protocol();
        $protocol
            ->setLabel($label)
            ->setReference($reference)
            ->setDescription($description)
            ->setStatus($protocolStatus)
        ;
        $this->em->persist($protocol);
    }

    /**
     * @throws IOExceptionInterface
     * @throws Exception
     */
    protected function registerDocker(
        string $composeFilePath,
        array $dockerConfigFiles,
        array $customCommands
    ): void {
        $this->checkIsSecureMode();
        $this->checkTypeMode(__METHOD__);

        try {
            $this->checkIsFileExists($composeFilePath);
            $this->checkIsFileExists($dockerConfigFiles);

            $dockerPath = $this->parameters->get('system.docker_path');
            $dockerPluginBasePath = $dockerPath.'/'.$this->getPluginReference();
            $dockerPluginConfigPath = $dockerPluginBasePath.'/config';
            $dockerPluginVolumePath = $dockerPluginBasePath.'/volume';
            $this->actions['docker']['directories'] = [
                $dockerPluginBasePath,
                $dockerPluginConfigPath,
                $dockerPluginVolumePath
            ];

            $this->checkIsFileExists($composeFilePath);
            $composeContent = Yaml::parseFile($composeFilePath);

            $this->checkComposeConflicts($composeContent);

            foreach ($dockerConfigFiles as $dockerConfigFile) {
                $this->actions['docker']['compose_files'][$dockerConfigFile] = $dockerPluginConfigPath.'/'.basename($dockerConfigFile);
            }

            $this->actions['docker']['compose_files'][$composeFilePath] = $dockerPluginBasePath.'/'.basename($composeFilePath);

            foreach ($composeContent['services'] as $serviceKey => $serviceConfig) {
                $this->actions['docker']['compose_config'][$serviceKey] = $serviceConfig;
                $this->rollbackActions['compose_services'][] = $serviceKey;
            }

            foreach ($customCommands as $serviceKey => $serviceCustomCommands) {
                if (false === isset($composeContent['services'][$serviceKey])) {
                    throw new InvalidArgumentException(sprintf('Service %s does not exist in your yaml compose', $serviceKey));
                }

                foreach ($serviceCustomCommands as $serviceCustomCommand) {
                    if (
                        false === array_key_exists('reference', $serviceCustomCommand) ||
                        false === array_key_exists('command', $serviceCustomCommand)
                    ) {
                        throw new InvalidArgumentException('Custom command must have a "reference" and "command" key.');
                    }
                }

                $this->actions['docker']['custom_commands'][$serviceKey] = $serviceCustomCommands;
            }
        } catch (IOExceptionInterface $exception) {
            $this->io->error($exception->getMessage());
            throw $exception;
        }
    }

    /**
     * @throws IOExceptionInterface
     */
    protected function registerSupervisorWorker(string $configDirectoryPath): void
    {
        try {
            $dockerPath = $this->parameters->get('system.docker_path').'/supervisor/config/conf.d';

            $this->checkIsDirectory($configDirectoryPath);

            $this->actions['supervisor'] = $this->getConfigFiles($configDirectoryPath, $dockerPath);
        } catch (IOExceptionInterface $exception) {
            $this->io->error($exception->getMessage());
            throw $exception;
        }
    }

    protected function getPluginVersion(): ?string
    {
        if (null !== $this->pluginVersion) {
            return $this->pluginVersion;
        }

        $composerPath = $this->getPluginRootPath().'/composer.json';

        if (false === is_file($composerPath)) {
            throw new InvalidArgumentException('Composer file "'.$composerPath.'" not found.');
        }

        $content = Yaml::parseFile($composerPath);
        $this->pluginVersion = $content['version'];

        return $this->pluginVersion;
    }

    protected function createUser(
        string $firstname,
        string $lastname,
        string $email,
        string $password,
        string $typeReference
    ): void {
        $userExist = $this->em->getRepository(User::class)->count(['email' => $email]);

        if ($userExist > 0) {
            throw new InvalidArgumentException('User with email "'.$email.'" already exists');
        }

        $type = $this->em->getRepository(UserType::class)->findOneBy(['reference' => $typeReference]);

        if (!$type instanceof UserType) {
            throw new InvalidArgumentException('User type "'.$typeReference.'" not found.');
        }

        $securityUser = new SecurityUser();
        $user = new User();
        $user
            ->setFirstname($firstname)
            ->setLastname($lastname)
            ->setEmail($email)
            ->setType($type)
            ->setRoles(['ROLE_USER'])
            ->setPassword($this->passwordHasher->hashPassword($securityUser, $password))
        ;

        $this->actions['create_users'][] = $user;
    }

    /**
     * @throws Exception
     */
    private function checkIsSecureMode(): void
    {
        if (false === $this->isSecureMode) {
            throw new Exception('You need to use the startInstall or startUninstall method to carry out your operations');
        }
    }

    /**
     * @throws Exception
     */
    private function checkTypeMode(string $method): void
    {
        if (
            $this->type === self::TYPE_INSTALL &&
            false === in_array($method, self::METHODS_AVAILABLES_BY_TYPE[$this->type])
        ) {
            throw new Exception(sprintf('You cannot use this method "%s" in installation mode', $method));
        }

        if (
            $this->type === self::TYPE_UNINSTALL &&
            false === in_array($method, self::METHODS_AVAILABLES_BY_TYPE[$this->type])
        ) {
            throw new Exception(sprintf('You cannot use this method "%s" in uninstall mode', $method));
        }
    }

    private function getConfigFiles(string $configDirectoryPath, string $dockerPluginConfigPath): array
    {
        $finder = new Finder();
        $files = [];

        $this->checkIsDirectory($configDirectoryPath);

        foreach ($finder->in($configDirectoryPath)->files() as $file) {
            $files[$file->getRealPath()] = $dockerPluginConfigPath.'/'.basename($file->getRealPath());
        }

        return $files;
    }

    private function copyFiles(array $files): void
    {
        foreach ($files as $from => $to) {
            $this->fileSystem->copy(
                $from,
                $to,
                true
            );
            $this->rollbackActions['files'][] = $to;
        }
    }

    /**
     * @throws \Doctrine\DBAL\Exception|ExceptionInterface
     */
    private function secureAction(
        Callable $callback,
        InputInterface $input,
        OutputInterface $output
    ): void {
        $this->isSecureMode = true;
        $this->force = (bool) $input->getOption('force');

        try {
            $this->checkPluginRequirement();
            $this->em->getConnection()->beginTransaction();
            $this->io = new SymfonyStyle($input, $output);

            $callback();
            $this->commit();

            if (false === $this->dryRun) {
                $this->em->getConnection()->commit();
            } else {
                $this->em->getConnection()->rollback();
            }

            $this->isSecureMode = false;
        } catch (Exception $exception) {
            $this->em->getConnection()->rollback();
            $this->rollback();

            throw $exception;
        }
    }

    private function createDockerDirectory(array $directories): void
    {
        foreach ($directories as $directory) {
            $this->fileSystem->mkdir($directory, 0755);
            $this->rollbackActions['directories'][] = $directory;
        }
    }

    /**
     * @throws ExceptionInterface
     */
    private function commit(): void
    {
        $messages = [];

        foreach ($this->actions as $action => $value) {
            switch ($action) {
                case 'docker':
                    if (!empty($value['directories'])) {
                        $this->createDockerDirectory($value['directories']);
                    }

                    if (!empty($value['compose_files'])) {
                        $this->copyFiles($value['compose_files']);
                    }

                    if (!empty($value['compose_config'])) {
                        foreach ($value['compose_config'] as $service => $composeConfig) {
                            $this->updateComposeYaml($service, $composeConfig);
                            $commands = [];
                            $commands[] = $this->actions['docker']['custom_commands'][$service] ?? [];

                            $messages[] = new InstallDockerRequestCommand(
                                payload: [
                                    'service' => $service,
                                    'commands' => $commands
                                ]
                            );
                        }
                    }
                    break;
                case 'supervisor':
                    $this->copyFiles($value);
                    break;
            }
        }

        $this->em->flush();

        foreach ($messages as $message) {
            $this->bus->dispatch($message);
        }
    }

    private function rollback(): void
    {
        try {
            $filesystem = new Filesystem();
            $filesystem->remove($this->rollbackActions['files']);
            $filesystem->remove($this->rollbackActions['directories']);

            $mainComposeFile = $this->parameters->get('system.compose_file_path');
            $mainComposeContent = Yaml::parseFile($mainComposeFile);

            foreach ($this->rollbackActions['compose_services'] as $composeService) {
                if (isset($mainComposeContent['services'][$composeService])) {
                    unset($mainComposeContent['services'][$composeService]);
                }
            }

            file_put_contents($mainComposeFile, Yaml::dump($mainComposeContent));
        } catch (Exception $exception) {
            $this->io->error($exception->getMessage());
        }
    }

    private function checkIsDirectory(string $directoryPath): void
    {
        if (false === is_dir($directoryPath)) {
            throw new IOException('Config directory "'.$directoryPath.'" is not a directory.');
        }
    }

    private function checkIsFileExists(string|array $filePaths): void
    {
        if (is_string($filePaths)) {
            $filePaths = [$filePaths];
        }

        foreach ($filePaths as $filePath) {
            if (false === file_exists($filePath)) {
                throw new IOException('File "'.$filePath.'" does not exists.');
            }
        }
    }

    private function checkComposeConflicts(array $compose): void
    {
        $mainComposeFile = $this->parameters->get('system.compose_file_path');
        $mainComposeContent = Yaml::parseFile($mainComposeFile);

        if (false === isset($compose['services'])) {
            throw new InvalidArgumentException('Yaml compose malformed.');
        }

        foreach ($compose['services'] as $serviceKey => $service) {
            if (isset($mainComposeContent['services'][$serviceKey])) {
                throw new InvalidArgumentException('Service "'.$service.'" already exists.');
            }
        }
    }

    private function updateComposeYaml(string $service, array $config): void
    {
        $mainComposeFile = $this->parameters->get('system.compose_file_path');
        $mainComposeContent = Yaml::parseFile($mainComposeFile);

        $mainComposeContent['services'][$service] = $config;
        file_put_contents($mainComposeFile, Yaml::dump($mainComposeContent));
    }

    /**
     * @return string plugin root path
     */
    abstract protected function getPluginRootPath(): string;

    /**
     * @return string plugin reference
     */
    abstract protected function getPluginReference(): string;

    /**
     * @return array plugin requirements
     */
    abstract protected function getPluginRequirements(): array;
}
