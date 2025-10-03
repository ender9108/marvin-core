<?php

namespace App\System\Infrastructure\Symfony\Command;

use Doctrine\ORM\EntityManagerInterface;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use Exception;
use InvalidArgumentException;
use Marvin\Shared\Domain\ValueObject\Description;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Reference;
use Marvin\System\Application\Command\CreatePlugin;
use Marvin\System\Application\Command\InstallDockerRequest;
use Marvin\System\Domain\Exception\PluginInstallationMethodNotAllowed;
use Marvin\System\Domain\Exception\PluginRequirementMissing;
use Marvin\System\Domain\Exception\PluginUninstallationMethodNotAllowed;
use Marvin\System\Domain\List\PluginStatusReference;
use Marvin\System\Domain\Model\Plugin;
use Marvin\System\Domain\Model\PluginStatus;
use Marvin\System\Domain\Repository\PluginRepositoryInterface;
use Marvin\System\Domain\ValueObject\Metadata;
use Marvin\System\Domain\ValueObject\Version;
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
use Symfony\Component\Yaml\Yaml;

abstract class AbstractPluginManagerCommand extends Command
{
    private const string TYPE_INSTALL = 'install';
    private const string TYPE_UNINSTALL = 'uninstall';
    private const string TYPE_UPDATE = 'update';
    private const array METHODS_AVAILABLES_BY_TYPE = [
        self::TYPE_INSTALL => [
            'App\\System\\Infrastructure\\Symfony\\Command\\AbstractPluginManagerCommand::registerPlugin',
            'App\\System\\Infrastructure\\Symfony\\Command\\AbstractPluginManagerCommand::registerContainer',
            'App\\System\\Infrastructure\\Symfony\\Command\\AbstractPluginManagerCommand::registerSupervisor',
        ],
        self::TYPE_UNINSTALL => [
            'App\\System\\Infrastructure\\Symfony\\Command\\AbstractPluginManagerCommand::unregisterPlugin',
            'App\\System\\Infrastructure\\Symfony\\Command\\AbstractPluginManagerCommand::unregisterContainer',
            'App\\System\\Infrastructure\\Symfony\\Command\\AbstractPluginManagerCommand::unregisterSupervisor',
        ],
        self::TYPE_UPDATE => [
            'App\\System\\Infrastructure\\Symfony\\Command\\AbstractPluginManagerCommand::registerPlugin',
            'App\\System\\Infrastructure\\Symfony\\Command\\AbstractPluginManagerCommand::registerContainer',
            'App\\System\\Infrastructure\\Symfony\\Command\\AbstractPluginManagerCommand::registerSupervisor',
        ],
    ];

    protected ?Plugin $plugin = null;
    protected ?SymfonyStyle $io = null;
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

    private readonly FileSystem $fileSystem;
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

    public function __construct(
        protected readonly EntityManagerInterface $em,
        protected readonly ParameterBagInterface $parameters,
        private readonly PluginRepositoryInterface $pluginRepository,
        private readonly SyncCommandBusInterface $syncCommandBus,
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

    protected function startInstall(
        callable $callback,
        string $pluginReference,
        InputInterface $input,
        OutputInterface $output
    ): void {
        $this->type = self::TYPE_INSTALL;
        $this->dryRun = $input->getOption('dry-run');

        $this->secureAction($callback, $pluginReference, $input, $output);
    }

    protected function startUninstall(
        callable $callback,
        string $pluginReference,
        InputInterface $input,
        OutputInterface $output
    ): void {
        $this->type = self::TYPE_UNINSTALL;
        $this->dryRun = $input->getOption('dry-run');

        $this->secureAction($callback, $pluginReference, $input, $output);
    }

    protected function startUpdate(
        callable $callback,
        string $pluginReference,
        InputInterface $input,
        OutputInterface $output
    ): void {
        $this->type = self::TYPE_UPDATE;
        $this->dryRun = $input->getOption('dry-run');

        $this->secureAction($callback, $pluginReference, $input, $output);
    }

    /**
     * @throws Exception
     */
    protected function checkPluginRequirement(array $requirements): void
    {
        $missingReferences = [];

        /** @var string $requirementReference */
        foreach ($requirements as $requirementReference) {
            $exists = $this->pluginRepository->exists(new Reference($requirementReference));

            if (false === $exists) {
                $missingReferences[] = new Reference($requirementReference);
            }
        }

        if (!empty($missingReferences)) {
            throw PluginRequirementMissing::withReferences($missingReferences);
        }

        $this->io->success('Requirements are ok !');
    }

    /**
     * @throws Exception
     */
    protected function registerPlugin(
        string $label,
        string $version,
        array $metadata = [],
        ?string $description = null,
    ): void {
        $this->checkIsSecureMode();
        $this->checkTypeMode(__METHOD__);

        $this->plugin = $this->syncCommandBus->handle(new CreatePlugin(
            new Label($label),
            new Reference($this->reference),
            new Version($version),
            new Reference(PluginStatusReference::STATUS_ENABLED->value),
            new Metadata($metadata),
            null !== $description ? new Description($description) : null
        ));
    }

    protected function registerProtocol()
    {
        /* @todo make domotic bounded context */
        /* @todo make system communication between bounded context */
    }

    protected function createProtocolUser()
    {
        /* @todo make domotic bounded context */
        /* @todo make system communication between bounded context */
    }

    /**
     * @throws IOExceptionInterface
     * @throws Exception
     */
    protected function registerContainer(
        string $composeFilePath,
        array $configFiles,
        array $customCommands
    ): void {
        $this->checkIsSecureMode();
        $this->checkTypeMode(__METHOD__);

        try {
            foreach ($configFiles as $configFile) {
                $this->checkIsFileExists($configFile);
            }

            $dockerPath = $this->parameters->get('docker_path');
            $dockerPluginBasePath = $dockerPath.'/'.$this->plugin->reference->value;
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

            foreach ($configFiles as $configFile) {
                $this->actions['docker']['compose_files'][$configFile] = $dockerPluginConfigPath.'/'.basename((string) $configFile);
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
    protected function registerSupervisor(string $configDirectoryPath): void
    {
        try {
            $dockerPath = $this->parameters->get('docker_path').'/supervisor/config/conf.d';

            $this->checkIsDirectory($configDirectoryPath);

            $this->actions['supervisor'] = $this->getConfigFiles($configDirectoryPath, $dockerPath);
        } catch (IOExceptionInterface $exception) {
            $this->io->error($exception->getMessage());
            throw $exception;
        }
    }

    protected function getBundleVersion(string $composerPath): ?string
    {
        if (false === is_file($composerPath)) {
            throw new InvalidArgumentException('Composer file "'.$composerPath.'" not found.');
        }

        $content = Yaml::parseFile($composerPath);

        return $content['version'] ?? null;
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
            throw PluginInstallationMethodNotAllowed::withMethodName($method);
        }

        if (
            $this->type === self::TYPE_UNINSTALL &&
            false === in_array($method, self::METHODS_AVAILABLES_BY_TYPE[$this->type])
        ) {
            throw PluginUninstallationMethodNotAllowed::withMethodName($method);
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
        callable $callback,
        string $pluginReference,
        InputInterface $input,
        OutputInterface $output
    ): void {
        $this->isSecureMode = true;
        $this->force = (bool) $input->getOption('force');
        $this->reference = $pluginReference;

        try {
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
        } catch (DomainException $domainException) {
            $this->em->getConnection()->rollback();
            $this->rollback();

            throw $domainException;
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
                case 'record_plugin':
                    if (true === $value && null !== $this->plugin) {
                        $this->em->persist($this->plugin);
                    }
                    break;
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

                            $messages[] = new InstallDockerRequest(
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

            $mainComposeFile = $this->parameters->get('compose_file_path');
            $mainComposeContent = Yaml::parseFile($mainComposeFile);

            foreach ($this->rollbackActions['compose_services'] as $composeService) {
                if (isset($mainComposeContent['services'][$composeService])) {
                    unset($mainComposeContent['services'][$composeService]);
                }
            }

            file_put_contents($mainComposeFile, Yaml::dump($mainComposeContent));
        } catch (DomainException $domainException) {
            $this->io->error($domainException->getMessage());
        }
    }

    private function checkIsDirectory(string $directoryPath): void
    {
        Assert::directory($directoryPath);
    }

    private function checkIsFileExists(string $filePath): void
    {
        Assert::fileExists($filePath);
    }

    private function checkComposeConflicts(array $compose): void
    {
        $mainComposeFile = $this->parameters->get('compose_file_path');
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
        $mainComposeFile = $this->parameters->get('compose_file_path');
        $mainComposeContent = Yaml::parseFile($mainComposeFile);

        $mainComposeContent['services'][$service] = $config;
        file_put_contents($mainComposeFile, Yaml::dump($mainComposeContent));
    }
}
