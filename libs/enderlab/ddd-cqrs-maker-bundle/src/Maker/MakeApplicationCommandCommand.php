<?php

namespace EnderLab\DddCqrsMakerBundle\Maker;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

#[AsCommand(
    name: 'make:application-command',
    description: 'Crée une Command et son CommandHandler',
)]
class MakeApplicationCommandCommand extends Command
{
    private Filesystem $filesystem;
    private array $parameters = [];

    public function __construct(private string $projectDir)
    {
        parent::__construct();
        $this->filesystem = new Filesystem();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('context', InputArgument::OPTIONAL, 'Nom du bounded context')
            ->addArgument('name', InputArgument::OPTIONAL, 'Nom de la Command');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Générateur de Command et CommandHandler');

        // 1. Demander le bounded context
        $context = $this->askContext($io, $input);
        if (!$context) {
            return Command::FAILURE;
        }

        // 2. Demander le nom de la Command
        $commandName = $this->askCommandName($io, $input);
        if (!$commandName) {
            return Command::FAILURE;
        }

        // 3. Sync ou Async
        $isSync = $io->confirm('Command synchrone ?', false);

        // 4. Sous-dossier optionnel
        $subFolder = $io->ask('Sous-dossier (ex: Container, Worker) - optionnel', '');

        // 5. Demander les paramètres
        $io->section('Paramètres de la Command');
        $this->askParameters($io, $context);

        // 6. Demander les dépendances du Handler
        $io->section('Dépendances du Handler');
        $dependencies = $this->askDependencies($io, $context);

        // 7. Confirmer
        if (!$io->confirm('Générer la Command et son Handler ?', true)) {
            $io->warning('Génération annulée');
            return Command::SUCCESS;
        }

        // 8. Générer les fichiers
        try {
            $this->generateCommand($context, $commandName, $subFolder, $isSync);
            $this->generateHandler($context, $commandName, $subFolder, $isSync, $dependencies, $io);

            $commandClass = "Marvin\\{$context}\\Application\\Command" . ($subFolder ? "\\{$subFolder}" : "") . "\\{$commandName}";
            $handlerClass = "Marvin\\{$context}\\Application\\CommandHandler" . ($subFolder ? "\\{$subFolder}" : "") . "\\{$commandName}Handler";

            $io->success([
                'Command et Handler générés avec succès !',
                '',
                'Fichiers créés :',
                "- {$commandClass}",
                "- {$handlerClass}",
                '',
                'Notes :',
                $isSync ? '- Command SYNCHRONE (SyncCommandInterface)' : '- Command ASYNCHRONE (CommandInterface)',
                '- Le Handler sera automatiquement découvert',
                '- Configure le routing Messenger si besoin',
            ]);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $io->error('Erreur lors de la génération : ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function askContext(SymfonyStyle $io, InputInterface $input): ?string
    {
        $context = $input->getArgument('context');

        if (!$context) {
            $existingContexts = $this->getExistingContexts();

            if (!empty($existingContexts)) {
                $question = new ChoiceQuestion(
                    'Choisir un bounded context existant',
                    $existingContexts,
                );
                $context = $io->askQuestion($question);
            } else {
                $context = $io->ask('Nom du bounded context');
            }
        }

        return $context;
    }

    private function askCommandName(SymfonyStyle $io, InputInterface $input): ?string
    {
        $name = $input->getArgument('name');

        if (!$name) {
            $name = $io->ask('Nom de la Command (ex: RestartContainer, RegisterDevice)');
        }

        return $name;
    }

    private function askParameters(SymfonyStyle $io, string $context): void
    {
        $io->note([
            'Définis les paramètres de la Command',
            'Types possibles : string, int, bool, array',
            'Ou des ValueObjects/Identity existants',
        ]);

        while (true) {
            $paramName = $io->ask('Nom du paramètre (ou "stop" pour terminer)', 'stop');

            if (strtolower($paramName) === 'stop') {
                break;
            }

            $paramType = $io->ask('Type du paramètre');

            $this->parameters[] = [
                'name' => $paramName,
                'type' => $paramType,
            ];

            $io->success("Paramètre '{$paramName}: {$paramType}' ajouté !");
        }
    }

    private function askDependencies(SymfonyStyle $io, string $context): array
    {
        $dependencies = [];

        $io->note([
            'Définis les dépendances du Handler',
            'Exemples : Repository, MessageBus, Logger, etc.',
        ]);

        while (true) {
            $depName = $io->ask('Nom de la dépendance (ou "stop" pour terminer)', 'stop');

            if (strtolower($depName) === 'stop') {
                break;
            }

            $depType = $io->ask('Type/Classe de la dépendance');
            $propName = $io->ask('Nom de la propriété', lcfirst($depName));

            $dependencies[] = [
                'name' => $depName,
                'type' => $depType,
                'property' => $propName,
            ];

            $io->success("Dépendance '{$depName}' ajoutée !");
        }

        return $dependencies;
    }

    private function getExistingContexts(): array
    {
        $srcDir = $this->projectDir . '/src';

        if (!is_dir($srcDir)) {
            return [];
        }

        $finder = new Finder();
        $finder->directories()->in($srcDir)->depth('== 0');

        $contexts = [];
        foreach ($finder as $dir) {
            $contexts[] = $dir->getFilename();
        }

        return $contexts;
    }

    private function generateCommand(string $context, string $commandName, string $subFolder, bool $isSync): void
    {
        $dir = $this->projectDir . "/src/{$context}/Application/Command";

        if ($subFolder) {
            $dir .= "/{$subFolder}";
        }

        $this->filesystem->mkdir($dir);

        $interface = $isSync ? 'SyncCommandInterface' : 'CommandInterface';
        $constructorParams = $this->generateConstructorParams();

        $content = <<<PHP
<?php

namespace Marvin\\{$context}\\Application\\Command{$this->getSubFolderNamespace($subFolder)};

use Enderlab\DddCqrs\Application\Command\\{$interface};

final readonly class {$commandName} implements {$interface}
{
    public function __construct(
{$constructorParams}
    ) {}
}

PHP;

        $file = $dir . "/{$commandName}.php";
        $this->filesystem->dumpFile($file, $content);
    }

    private function generateHandler(
        string $context,
        string $commandName,
        string $subFolder,
        bool $isSync,
        array $dependencies,
        SymfonyStyle $io
    ): void {
        $dir = $this->projectDir . "/src/{$context}/Application/CommandHandler";

        if ($subFolder) {
            $dir .= "/{$subFolder}";
        }

        $this->filesystem->mkdir($dir);

        $interface = $isSync ? 'SyncCommandHandlerInterface' : 'CommandHandlerInterface';
        $useStatements = $this->generateHandlerUseStatements($dependencies);
        $constructorDeps = $this->generateHandlerConstructor($dependencies);
        $handlerLogic = $this->generateHandlerLogic($commandName, $dependencies, $io);

        $content = <<<PHP
<?php

namespace Marvin\\{$context}\\Application\\CommandHandler{$this->getSubFolderNamespace($subFolder)};

use Marvin\\{$context}\\Application\\Command{$this->getSubFolderNamespace($subFolder)}\\{$commandName};
use Enderlab\DddCqrs\Application\Command\\{$interface};
{$useStatements}

final readonly class {$commandName}Handler implements {$interface}
{
{$constructorDeps}

    public function __invoke({$commandName} \$command): void
    {
{$handlerLogic}
    }
}

PHP;

        $file = $dir . "/{$commandName}Handler.php";
        $this->filesystem->dumpFile($file, $content);
    }

    private function generateConstructorParams(): string
    {
        if (empty($this->parameters)) {
            return '';
        }

        $params = [];
        foreach ($this->parameters as $param) {
            $params[] = "        public {$param['type']} \${$param['name']},";
        }

        return implode("\n", $params);
    }

    private function generateHandlerUseStatements(array $dependencies): string
    {
        $uses = ["use Psr\Log\LoggerInterface;"];

        foreach ($dependencies as $dep) {
            if (str_contains($dep['type'], '\\')) {
                $uses[] = "use {$dep['type']};";
            }
        }

        return implode("\n", array_unique($uses));
    }

    private function generateHandlerConstructor(array $dependencies): string
    {
        if (empty($dependencies)) {
            return '';
        }

        $params = [];
        foreach ($dependencies as $dep) {
            $params[] = "        private {$dep['type']} \${$dep['property']},";
        }

        return "    public function __construct(\n" . implode("\n", $params) . "\n    ) {}";
    }

    private function generateHandlerLogic(string $commandName, array $dependencies, SymfonyStyle $io): string
    {
        $io->writeln('');
        $io->note('Génération de la logique basique du Handler. Tu devras compléter l\'implémentation.');

        $logic = [
            "        // TODO: Implémenter la logique de {$commandName}",
            "",
        ];

        // Si repository trouvé
        foreach ($dependencies as $dep) {
            if (str_contains($dep['type'], 'Repository')) {
                $logic[] = "        // Exemple avec Repository :";
                $logic[] = "        // \$entity = \$this->{$dep['property']}->byId(\$command->entityId);";
                $logic[] = "        // \$entity->doSomething();";
                $logic[] = "        // \$this->{$dep['property']}->save(\$entity);";
                break;
            }
        }

        // Si MessageBus trouvé
        foreach ($dependencies as $dep) {
            if (str_contains($dep['type'], 'MessageBus')) {
                $logic[] = "";
                $logic[] = "        // Exemple avec MessageBus :";
                $logic[] = "        // \$this->{$dep['property']}->dispatch(new SomeEvent(...));";
                break;
            }
        }

        return implode("\n", $logic);
    }

    private function getSubFolderNamespace(string $subFolder): string
    {
        return $subFolder ? "\\{$subFolder}" : '';
    }
}
