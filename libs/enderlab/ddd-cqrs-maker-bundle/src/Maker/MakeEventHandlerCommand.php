<?php

namespace Enderlab\DddCqrsMakerBundle\Maker;

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
    name: 'make:event-handler',
    description: 'Crée un EventHandler pour un événement de domaine',
)]
class MakeEventHandlerCommand extends Command
{
    private Filesystem $filesystem;

    public function __construct(private readonly string $projectDir)
    {
        parent::__construct();
        $this->filesystem = new Filesystem();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('context', InputArgument::OPTIONAL, 'Nom du bounded context')
            ->addArgument('event', InputArgument::OPTIONAL, 'Nom de l\'événement');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Générateur d\'EventHandler');

        $context = $this->askContext($io, $input);
        if (!$context) {
            return Command::FAILURE;
        }

        $eventName = $this->askEventName($io, $input);
        if (!$eventName) {
            return Command::FAILURE;
        }

        $sourceContext = $io->ask('Context source de l\'événement (laisser vide si même context)', $context);
        $subFolder = $io->ask('Sous-dossier (optionnel)', '');

        $io->section('Dépendances de l\'EventHandler');
        $dependencies = $this->askDependencies($io);

        if (!$io->confirm('Générer l\'EventHandler ?', true)) {
            $io->warning('Génération annulée');
            return Command::SUCCESS;
        }

        try {
            $this->generateEventHandler($context, $sourceContext, $eventName, $subFolder, $dependencies);

            $handlerClass = "Marvin\\{$context}\\Application\\EventHandler" . ($subFolder ? "\\{$subFolder}" : "") . "\\{$eventName}Handler";

            $io->success([
                'EventHandler généré avec succès !',
                '',
                "Fichier créé : {$handlerClass}",
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
                $question = new ChoiceQuestion('Choisir un bounded context', $existingContexts);
                $context = $io->askQuestion($question);
            } else {
                $context = $io->ask('Nom du bounded context');
            }
        }

        return $context;
    }

    private function askEventName(SymfonyStyle $io, InputInterface $input): ?string
    {
        $eventName = $input->getArgument('event');

        if (!$eventName) {
            $eventName = $io->ask('Nom de l\'événement (ex: ContainerStatusChanged)');
        }

        return $eventName;
    }

    private function askDependencies(SymfonyStyle $io): array
    {
        $dependencies = [];

        while (true) {
            $depName = $io->ask('Nom de la dépendance (ou "stop")', 'stop');

            if (strtolower($depName) === 'stop') {
                break;
            }

            $depType = $io->ask('Type/Classe');
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

    private function generateEventHandler(
        string $context,
        string $sourceContext,
        string $eventName,
        string $subFolder,
        array $dependencies
    ): void {
        $dir = $this->projectDir . "/src/{$context}/Application/EventHandler";

        if ($subFolder) {
            $dir .= "/{$subFolder}";
        }

        $this->filesystem->mkdir($dir);

        $useStatements = $this->generateUseStatements($dependencies);
        $constructorDeps = $this->generateConstructor($dependencies);

        $content = <<<PHP
<?php

namespace Marvin\\{$context}\\Application\\EventHandler{$this->getSubFolderNamespace($subFolder)};

use Marvin\\{$sourceContext}\\Domain\\Event\\{$eventName};
use Enderlab\DddCqrs\Application\Event\DomainEventHandlerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
{$useStatements}

#[AsMessageHandler]
final readonly class {$eventName}Handler implements DomainEventHandlerInterface
{
{$constructorDeps}

    public function __invoke({$eventName} \$event): void
    {
        // TODO: Implémenter la logique de réaction à {$eventName}
    }
}

PHP;

        $file = $dir . "/{$eventName}Handler.php";
        $this->filesystem->dumpFile($file, $content);
    }

    private function generateUseStatements(array $dependencies): string
    {
        $uses = ["use Psr\Log\LoggerInterface;"];

        foreach ($dependencies as $dep) {
            if (str_contains($dep['type'], '\\')) {
                $uses[] = "use {$dep['type']};";
            }
        }

        return implode("\n", array_unique($uses));
    }

    private function generateConstructor(array $dependencies): string
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

    private function getSubFolderNamespace(string $subFolder): string
    {
        return $subFolder ? "\\{$subFolder}" : '';
    }
}
