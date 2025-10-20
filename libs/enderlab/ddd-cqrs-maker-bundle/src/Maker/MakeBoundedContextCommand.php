<?php

namespace EnderLab\DddCqrsMakerBundle\Maker;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'make:bounded-context',
    description: 'Crée toute l\'arborescence d\'un bounded context DDD',
)]
class MakeBoundedContextCommand extends Command
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
        'Domain/List',
        'Domain/Service',
        'Infrastructure/Framework/Symfony/Service',
        'Infrastructure/Framework/Symfony/EventListener',
        'Infrastructure/Framework/Symfony/Validator',
        'Infrastructure/Framework/Symfony/MapperTransformer',
        'Infrastructure/Framework/Symfony/DataFixtures',
        'Infrastructure/Persistence/Doctrine/DBAL/Types',
        'Infrastructure/Persistence/Doctrine/ORM',
        'Infrastructure/Persistence/Doctrine/EventListener',
        'Infrastructure/Messaging/Producer',
        'Infrastructure/Messaging/Consumer',
        'Presentation/Cli/Command',
        'Presentation/Api/Resource',
        'Presentation/Api/State/Processor',
        'Presentation/Api/State/Provider',
    ];

    public function __construct(private readonly string $projectDir)
    {
        parent::__construct();
        $this->filesystem = new Filesystem();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::OPTIONAL, 'Nom du bounded context');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Générateur de Bounded Context DDD');

        $contextName = $input->getArgument('name');

        if (!$contextName) {
            $contextName = $io->ask('Nom du bounded context (ex: System, Device, Protocol)');
        }

        if (!$contextName) {
            $io->error('Le nom du bounded context est requis');
            return Command::FAILURE;
        }

        $contextDir = $this->projectDir . "/src/{$contextName}";

        if (is_dir($contextDir)) {
            if (!$io->confirm("Le context {$contextName} existe déjà. Continuer quand même ?", false)) {
                $io->warning('Génération annulée');
                return Command::SUCCESS;
            }
        }

        if (!$io->confirm("Créer le bounded context '{$contextName}' avec toute son arborescence ?", true)) {
            $io->warning('Génération annulée');
            return Command::SUCCESS;
        }

        try {
            $this->createDirectories($contextName, $io);
            $this->createGitKeepFiles($contextName);
            $this->createReadmeFile($contextName);
            $this->createDoctrineConfigDirectory($contextName);

            $io->success([
                "Bounded context '{$contextName}' créé avec succès !",
                '',
                'Arborescence créée :',
                "- src/{$contextName}/Application/",
                "- src/{$contextName}/Domain/",
                "- src/{$contextName}/Infrastructure/",
                "- src/{$contextName}/Presentation/",
                "- config/doctrine/{$contextName}/",
            ]);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $io->error('Erreur lors de la génération : ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function createDirectories(string $contextName, SymfonyStyle $io): void
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

    private function createReadmeFile(string $contextName): void
    {
        $content = <<<MD
# Bounded Context: {$contextName}

## Description

Ce bounded context gère [décrire la responsabilité du context].

## Usage

\`\`\`bash
# Créer un Model
php bin/console make:model {$contextName} [ModelName]

# Créer une Command
php bin/console make:application-command {$contextName} [CommandName]

# Créer une Query
php bin/console make:query {$contextName} [QueryName]

# Créer un ValueObject
php bin/console make:value-object {$contextName} [ValueObjectName]
\`\`\`

MD;

        $file = $this->projectDir . "/src/{$contextName}/README.md";
        $this->filesystem->dumpFile($file, $content);
    }

    private function createDoctrineConfigDirectory(string $contextName): void
    {
        $dir = $this->projectDir . "/config/doctrine/{$contextName}";
        $this->filesystem->mkdir($dir);

        $gitKeepFile = $dir . '/.gitkeep';
        $this->filesystem->touch($gitKeepFile);
    }
}
