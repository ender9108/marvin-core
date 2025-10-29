<?php

namespace EnderLab\DddCqrsMakerBundle\Maker;

use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Filesystem\Filesystem;

class MakeBoundedContextCommand extends AbstractMaker
{
    private Filesystem $filesystem;

    private const array DIRECTORIES = [
        'Application/Command',
        'Application/CommandHandler',
        'Application/Query',
        'Application/QueryHandler',
        'Application/EventHandler',
        'Domain/Model',
        'Domain/ValueObject',
        'Domain/ValueObject/Identity',
        'Domain/Repository',
        'Domain/Event',
        'Domain/Exception',
        'Domain/Service',
        'Infrastructure/Framework/Symfony/DataFixtures',
        //'Infrastructure/Framework/Symfony/Service',
        //'Infrastructure/Framework/Symfony/EventListener',
        //'Infrastructure/Framework/Symfony/Validator',
        //'Infrastructure/Framework/Symfony/MapperTransformer',
        //'Infrastructure/Framework/Symfony/DataFixtures',
        'Infrastructure/Persistence/Doctrine/DBAL/Types',
        'Infrastructure/Persistence/Doctrine/ORM',
        //'Infrastructure/Persistence/Doctrine/EventListener',
        //'Infrastructure/Messaging/Producer',
        //'Infrastructure/Messaging/Consumer',
        'Presentation/Cli/Command',
        //'Presentation/Api/Resource',
        //'Presentation/Api/State/Processor',
        //'Presentation/Api/State/Provider',
    ];

    public function __construct(private readonly string $projectDir)
    {
        $this->filesystem = new Filesystem();
    }

    public static function getCommandName(): string
    {
        return 'make:bounded-context';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->setDescription('Crée toute l\'arborescence d\'un bounded context DDD')
            ->addArgument('name', InputArgument::OPTIONAL, 'Nom du bounded context');
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $io->title('Générateur de Bounded Context DDD');

        $contextName = $input->getArgument('name');

        if (!$contextName) {
            $contextName = $io->ask('Nom du bounded context (ex: System, Device, Protocol)');
        }

        if (!$contextName) {
            $io->error('Le nom du bounded context est requis');
            return;
        }

        $contextDir = $this->projectDir . "/src/{$contextName}";

        if (is_dir($contextDir)) {
            if (!$io->confirm("Le context {$contextName} existe déjà. Continuer quand même ?", false)) {
                $io->warning('Génération annulée');
                return;
            }
        }

        if (!$io->confirm("Créer le bounded context '{$contextName}' avec toute son arborescence ?", true)) {
            $io->warning('Génération annulée');
            return;
        }

        try {
            $this->createDirectories($contextName, $io);
            $this->createGitKeepFiles($contextName);
            $this->createDoctrineConfigDirectory($contextName);

            $io->success("Bounded context '{$contextName}' créé avec succès !");

            $io->writeln('');
            $io->writeln('<info>Arborescence créée :</info>');
            $io->listing([
                "src/{$contextName}/Application/",
                "src/{$contextName}/Domain/",
                "src/{$contextName}/Infrastructure/",
                "src/{$contextName}/Presentation/",
                "config/doctrine/ORM/{$contextName}/",
            ]);
        } catch (\Exception $e) {
            $io->error('Erreur lors de la génération : ' . $e->getMessage());
        }
    }

    private function createDirectories(string $contextName, ConsoleStyle $io): void
    {
        $io->section('Création des répertoires...');

        $progressBar = $io->createProgressBar(count(self::DIRECTORIES));
        $progressBar->start();

        foreach (self::DIRECTORIES as $dir) {
            $fullPath = $this->projectDir . "/src/{$contextName}/{$dir}";
            $this->filesystem->mkdir($fullPath);
            $progressBar->advance();
        }

        $progressBar->finish();
        $io->newLine(2);
    }

    private function createGitKeepFiles(string $contextName): void
    {
        foreach (self::DIRECTORIES as $dir) {
            $fullPath = $this->projectDir . "/src/{$contextName}/{$dir}";
            $gitKeepFile = $fullPath . '/.gitkeep';
            $this->filesystem->touch($gitKeepFile);
        }
    }

    private function createDoctrineConfigDirectory(string $contextName): void
    {
        $dir = $this->projectDir . "/config/doctrine/ORM/{$contextName}";
        $this->filesystem->mkdir($dir);

        $gitKeepFile = $dir . '/.gitkeep';
        $this->filesystem->touch($gitKeepFile);
    }
}
