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
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class MakeCliCommandCommand extends AbstractMaker
{
    private Filesystem $filesystem;
    public function __construct(private readonly string $projectDir)
    {
        $this->filesystem = new Filesystem();
    }
    public static function getCommandName(): string
    {
        return 'make:cli-command';
    }
    public static function getCommandDescription(): string
    {
        return 'Make new cli command';
    }
    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->setDescription('Crée une commande Symfony CLI qui utilise Commands/Queries')
            ->addArgument('context', InputArgument::OPTIONAL, 'Nom du bounded context')
            ->addArgument('name', InputArgument::OPTIONAL, 'Nom de la commande');
    }
    public function configureDependencies(DependencyBuilder $dependencies): void
    {
    }
    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $io->title('Générateur de Commande CLI Symfony');
        $context = $this->askContext($io, $input);
        if (!$context) {
            return;
        }
        $commandName = $this->askCommandName($io, $input);
        if (!$commandName) {
            return;
        }
        $io->section('Configuration de la commande');
        $commandSignature = $io->ask(
            'Signature de la commande',
            'marvin:' . strtolower($context) . ':' . $this->camelToKebab($commandName)
        );
        $description = $io->ask('Description de la commande');
        $useCase = $io->choice(
            'Type d\'use case appelé',
            ['Command', 'Query', 'Les deux', 'Aucun (logique custom)'],
            'Query'
        );
        $useCaseClass = null;
        $useCaseType = null;
        if ($useCase !== 'Aucun (logique custom)' && $useCase !== 'Les deux') {
            $useCaseType = $useCase;
            $useCaseClass = $io->ask("Nom de la {$useCase} à appeler (ex: GetContainer, ListWorkers)");
        }
        $hasArguments = $io->confirm('La commande a des arguments ?', false);
        $arguments = [];
        if ($hasArguments) {
            $arguments = $this->askArguments($io);
        }
        $hasOptions = $io->confirm('La commande a des options ?', false);
        $options = [];
        if ($hasOptions) {
            $options = $this->askOptions($io);
        }
        if (!$io->confirm('Générer la commande CLI ?', true)) {
            $io->warning('Génération annulée');
            return;
        }
        try {
            $this->generateCliCommand(
                $context,
                $commandName,
                $commandSignature,
                $description,
                $useCaseType,
                $useCaseClass,
                $arguments,
                $options
            );
            $cliClass = "Marvin\\{$context}\\Presentation\\Cli\\Command\\{$commandName}Command";
            $io->success([
                'Commande CLI générée avec succès !',
                '',
                "Fichier créé : {$cliClass}",
                '',
                'Usage :',
                "php bin/console {$commandSignature}",
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
    private function askCommandName(ConsoleStyle $io, InputInterface $input): ?string
    {
        $name = $input->getArgument('name');
        if (!$name) {
            $name = $io->ask('Nom de la commande CLI (ex: ListContainers, RestartWorker)');
        }
        return $name;
    }
    private function askArguments(ConsoleStyle $io): array
    {
        $arguments = [];
        $io->writeln('Définis les arguments :');
        while (true) {
            $argName = $io->ask('Nom de l\'argument (ou "stop")', 'stop');
            if (strtolower($argName) === 'stop') {
                break;
            }
            $argDescription = $io->ask('Description');
            $argRequired = $io->confirm('Requis ?', true);
            $arguments[] = [
                'name' => $argName,
                'description' => $argDescription,
                'required' => $argRequired,
            ];
            $io->success("Argument '{$argName}' ajouté !");
        }
        return $arguments;
    }
    private function askOptions(ConsoleStyle $io): array
    {
        $options = [];
        $io->writeln('Définis les options :');
        while (true) {
            $optName = $io->ask('Nom de l\'option (ou "stop")', 'stop');
            if (strtolower($optName) === 'stop') {
                break;
            }
            $optDescription = $io->ask('Description');
            $optShortcut = $io->ask('Raccourci (ex: -f)', null);
            $optDefault = $io->ask('Valeur par défaut', null);
            $options[] = [
                'name' => $optName,
                'description' => $optDescription,
                'shortcut' => $optShortcut,
                'default' => $optDefault,
            ];
            $io->success("Option '{$optName}' ajoutée !");
        }
        return $options;
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
    private function generateCliCommand(
        string $context,
        string $commandName,
        string $commandSignature,
        string $description,
        ?string $useCaseType,
        ?string $useCaseClass,
        array $arguments,
        array $options
    ): void {
        $dir = $this->projectDir . "/src/{$context}/Presentation/Cli/Command";
        $this->filesystem->mkdir($dir);
        $useStatements = $this->generateUseStatements($context, $useCaseType, $useCaseClass);
        $configureBlock = $this->generateConfigureBlock($arguments, $options);
        $busInjection = $this->generateBusInjection($useCaseType);
        $executeLogic = $this->generateExecuteLogic($useCaseType, $useCaseClass, $arguments);
        $content = <<<PHP
<?php

declare(strict_types=1);

namespace Marvin\\{$context}\\Presentation\\Cli\\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\ConsoleStyle;
{$useStatements}
#[AsCommand(
    name: '{$commandSignature}',
    description: '{$description}',
)]
class {$commandName}Command extends Command
{
{$busInjection}
    protected function configure(): void
    {
{$configureBlock}
    }
    protected function execute(InputInterface \$input, OutputInterface \$output): int
    {
        \$io = new ConsoleStyle(\$input, \$output);
        \$io->title('{$description}');
{$executeLogic}
        return;
    }
}
PHP;
        $file = $dir . "/{$commandName}Command.php";
        $this->filesystem->dumpFile($file, $content);
    }
    private function generateUseStatements(string $context, ?string $useCaseType, ?string $useCaseClass): string
    {
        $uses = [];
        if ($useCaseType && $useCaseClass) {
            $uses[] = "use Marvin\\{$context}\\Application\\{$useCaseType}\\{$useCaseClass};";
        }
        if ($useCaseType === 'Command') {
            $uses[] = "use Enderlab\DddCqrs\Application\Command\CommandBusInterface;";
        } elseif ($useCaseType === 'Query') {
            $uses[] = "use Enderlab\DddCqrs\Application\Query\QueryBusInterface;";
        }
        return !empty($uses) ? implode("\n", $uses) : '';
    }
    private function generateConfigureBlock(array $arguments, array $options): string
    {
        $config = [];
        foreach ($arguments as $arg) {
            $mode = $arg['required'] ? 'InputArgument::REQUIRED' : 'InputArgument::OPTIONAL';
            $config[] = "        \$this->addArgument('{$arg['name']}', {$mode}, '{$arg['description']}');";
        }
        foreach ($options as $opt) {
            $shortcut = $opt['shortcut'] ? "'{$opt['shortcut']}'" : 'null';
            $default = $opt['default'] !== null ? "'" . $opt['default'] . "'" : 'null';
            $config[] = "        \$this->addOption('{$opt['name']}', {$shortcut}, InputOption::VALUE_OPTIONAL, '{$opt['description']}', {$default});";
        }
        return !empty($config) ? implode("\n", $config) : "        // Pas d'arguments ni d'options";
    }
    private function generateBusInjection(?string $useCaseType): string
    {
        if (!$useCaseType) {
            return '';
        }
        if ($useCaseType === 'Command') {
            return <<<PHP
    public function __construct(
        private readonly CommandBusInterface \$commandBus,
    ) {
        parent::__construct();
    }
PHP;
        }
        if ($useCaseType === 'Query') {
            return <<<PHP
    public function __construct(
        private readonly QueryBusInterface \$queryBus,
    ) {
        parent::__construct();
    }
PHP;
        }
        return '';
    }
    private function generateExecuteLogic(?string $useCaseType, ?string $useCaseClass, array $arguments): string
    {
        if (!$useCaseType || !$useCaseClass) {
            return "        // TODO: Implémenter la logique de la commande\n        \$io->success('Commande exécutée avec succès !');";
        }
        $argsList = [];
        foreach ($arguments as $arg) {
            $argsList[] = "            {$arg['name']}: \$input->getArgument('{$arg['name']}'),";
        }
        $argsBlock = !empty($argsList) ? "\n" . implode("\n", $argsList) . "\n        " : '';
        if ($useCaseType === 'Query') {
            return <<<PHP
        \$query = new {$useCaseClass}({$argsBlock});
        \$result = \$this->queryBus->ask(\$query);
        // TODO: Afficher le résultat
        \$io->success('Query exécutée avec succès !');
PHP;
        }
        if ($useCaseType === 'Command') {
            return <<<PHP
        \$command = new {$useCaseClass}({$argsBlock});
        \$this->commandBus->dispatch(\$command);
        \$io->success('Command exécutée avec succès !');
PHP;
        }
        return '';
    }
    private function camelToKebab(string $input): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $input));
    }
}
