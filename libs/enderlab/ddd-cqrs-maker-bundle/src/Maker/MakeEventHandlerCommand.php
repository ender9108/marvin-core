<?php

namespace Enderlab\DddCqrsMakerBundle\Maker;

use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class MakeEventHandlerCommand extends AbstractMaker
{
    private Filesystem $filesystem;

    public function __construct(private readonly string $projectDir)
    {
        $this->filesystem = new Filesystem();
    }

    public static function getCommandName(): string
    {
        return 'make:event-handler';
    }

    public static function getCommandDescription(): string
    {
        return 'Make new event handler';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->setDescription('Crée un EventHandler pour un événement de domaine')
            ->addArgument('context', InputArgument::OPTIONAL, 'Nom du bounded context')
            ->addArgument('event', InputArgument::OPTIONAL, 'Nom de l\'événement');
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {

        $io->title('Générateur d\'EventHandler');

        $context = $this->askContext($io, $input);
        if (!$context) {
            return;
        }

        $eventName = $this->askEventName($io, $input);
        if (!$eventName) {
            return;
        }

        $sourceContext = $io->ask('Context source de l\'événement (laisser vide si même context)', $context);
        $subFolder = $io->ask('Sous-dossier (optionnel)', '');

        $io->section('Dépendances de l\'EventHandler');
        $dependencies = $this->askDependencies($io);

        if (!$io->confirm('Générer l\'EventHandler ?', true)) {
            $io->warning('Génération annulée');
            return;
        }

        try {
            $this->generateEventHandler($context, $sourceContext, $eventName, $subFolder, $dependencies);

            $handlerClass = "Marvin\\{$context}\\Application\\EventHandler" . ($subFolder ? "\\{$subFolder}" : "") . "\\{$eventName}Handler";

            $io->success([
                'EventHandler généré avec succès !',
                '',
                "Fichier créé : {$handlerClass}",
            ]);

            return;

        } catch (\Exception $e) {
            $io->error('Erreur lors de la génération : ' . $e->getMessage());
            return;
        }
    }

    private function askContext(ConsoleStyle $io, InputInterface $input): ?string
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

    private function askEventName(ConsoleStyle $io, InputInterface $input): ?string
    {
        $eventName = $input->getArgument('event');

        if (!$eventName) {
            $eventName = $io->ask('Nom de l\'événement (ex: ContainerStatusChanged)');
        }

        return $eventName;
    }

    private function askDependencies(ConsoleStyle $io): array
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
