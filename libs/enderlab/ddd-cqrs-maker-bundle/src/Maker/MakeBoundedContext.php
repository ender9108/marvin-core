<?php

declare(strict_types=1);

namespace EnderLab\DddCqrsMakerBundle\Maker;

use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

final class MakeBoundedContext extends AbstractMaker
{
    public function __construct(
        private readonly string $projectDir,
    ) {
    }

    public static function getCommandName(): string
    {
        return 'make:bounded-context';
    }

    public static function getCommandDescription(): string
    {
        return 'Generate the standard directory structure of a new bounded context.';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->addArgument('name', InputArgument::OPTIONAL, 'The name of the bounded context to create (e.g. Billing)')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force creation even if target directory exists (must be empty).')
        ;
    }

    public function configureDependencies(DependencyBuilder $dependencies): void {
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $name = $input->getArgument('name');
        if (!$name) {
            $name = $io->ask('Name of the bounded context (e.g. Billing)', null, function (?string $value) {
                if (null === $value || '' === trim($value)) {
                    throw new \RuntimeException('The bounded context name cannot be empty.');
                }
                return $value;
            });
        }

        $normalized = preg_replace('/[^a-z0-9]+/i', ' ', (string) $name);
        $normalized = str_replace(' ', '', ucwords(strtolower((string) $normalized)));
        if (!$normalized) {
            throw new \RuntimeException('Invalid bounded context name.');
        }

        $filesystem = new Filesystem();
        $srcDir = rtrim($this->projectDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'src';
        $targetBC = $srcDir . DIRECTORY_SEPARATOR . $normalized;

        $force = (bool) $input->getOption('force');

        if (is_dir($targetBC)) {
            // Check emptiness
            $files = scandir($targetBC) ?: [];
            $files = array_diff($files, ['.', '..']);
            if (!$force) {
                if (!empty($files)) {
                    throw new \RuntimeException(sprintf('Target directory %s already exists and is not empty. Use --force to proceed if you know what you are doing.', $targetBC));
                }
                $io->warning(sprintf('Target directory %s already exists but is empty. Proceeding.', $targetBC));
            } else {
                if (!empty($files)) {
                    throw new \RuntimeException(sprintf('Refusing to proceed: --force is allowed only when the existing target directory is empty. Please clean %s first.', $targetBC));
                }
            }
        }

        // Ensure base target dir exists
        $filesystem->mkdir($targetBC);

        // Standard skeleton based on the Security BC structure (without copying it)
        $directories = [
            'Domain',
            'Domain/Model',
            'Domain/Repository',
            'Domain/Exception',
            'Domain/Event',
            'Domain/ValueObject',
            'Domain/ValueObject/Identity',
            'Application',
            'Application/Command',
            'Application/CommandHandler',
            'Application/Query',
            'Application/QueryHandler',
            'Infrastructure',
            'Infrastructure/Persistence',
            'Infrastructure/Persistence/Doctrine',
            'Infrastructure/Persistence/Doctrine/ORM',
            'Infrastructure/Persistence/Doctrine/DBAL',
            'Infrastructure/Persistence/Doctrine/DBAL/Types',
            'Infrastructure/Framework',
            'Infrastructure/Framework/Symfony',
            'Infrastructure/Framework/Symfony/DataFixtures',
            'Presentation',
        ];

        $createdCount = 0;
        foreach ($directories as $relativePath) {
            $newDir = $targetBC . DIRECTORY_SEPARATOR . $relativePath;
            $filesystem->mkdir($newDir);

            ++$createdCount;
        }

        // Create services configuration file under config/services/<bc>.php modeled after security.php
        $servicesDir = rtrim($this->projectDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'services';
        $filesystem->mkdir($servicesDir);
        $servicesFilename = strtolower($normalized) . '.php';
        $servicesPath = $servicesDir . DIRECTORY_SEPARATOR . $servicesFilename;
        if (!file_exists($servicesPath)) {
            $servicesPhp = <<<'PHP'
<?php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services
        ->defaults()
        ->autowire()
        ->autoconfigure()
    ;

    $services->load('Marvin\\__BC__\\Infrastructure\\Framework\\Symfony\\DataFixtures\\', dirname(__DIR__, 2) . '/src/__BC__/Infrastructure/Framework/Symfony/DataFixtures/');
    $services->load('Marvin\\__BC__\\', dirname(__DIR__, 2).'/src/__BC__');
};
PHP;
            $servicesPhp = str_replace('__BC__', $normalized, $servicesPhp);
            $filesystem->dumpFile($servicesPath, $servicesPhp);
        } else {
            $io->warning(sprintf('Services file already exists, skipping: %s', $servicesPath));
        }

        $io->success(sprintf('Bounded context "%s" generated at %s. %d directories created (including root). Services file: %s', $normalized, $targetBC, $createdCount + 1, $servicesPath));
    }
}
