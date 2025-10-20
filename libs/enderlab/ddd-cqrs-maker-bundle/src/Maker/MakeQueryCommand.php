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
    name: 'make:query',
    description: 'Crée une Query et son QueryHandler',
)]
class MakeQueryCommand extends Command
{
    private Filesystem $filesystem;
    private array $parameters = [];

    public function __construct(private readonly string $projectDir)
    {
        parent::__construct();
        $this->filesystem = new Filesystem();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('context', InputArgument::OPTIONAL, 'Nom du bounded context')
            ->addArgument('name', InputArgument::OPTIONAL, 'Nom de la Query');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Générateur de Query et QueryHandler');

        // 1. Demander le bounded context
        $context = $this->askContext($io, $input);
        if (!$context) {
            return Command::FAILURE;
        }

        // 2. Demander le nom de la Query
        $queryName = $this->askQueryName($io, $input);
        if (!$queryName) {
            return Command::FAILURE;
        }

        // 3. Sous-dossier optionnel
        $subFolder = $io->ask('Sous-dossier (ex: Container, Worker) - optionnel', '');

        // 4. Type de retour
        $returnType = $io->ask('Type de retour du QueryHandler', 'array');

        // 5. Demander les paramètres
        $io->section('Paramètres de la Query');
        $this->askParameters($io, $context);

        // 6. Demander les dépendances du QueryHandler
        $io->section('Dépendances du QueryHandler');
        $dependencies = $this->askDependencies($io, $context);

        // 7. Confirmer
        if (!$io->confirm('Générer la Query et son QueryHandler ?', true)) {
            $io->warning('Génération annulée');
            return Command::SUCCESS;
        }

        // 8. Générer les fichiers
        try {
            $this->generateQuery($context, $queryName, $subFolder);
            $this->generateQueryHandler($context, $queryName, $subFolder, $returnType, $dependencies, $io);

            $queryClass = "Marvin\\{$context}\\Application\\Query" . ($subFolder ? "\\{$subFolder}" : "") . "\\{$queryName}";
            $handlerClass = "Marvin\\{$context}\\Application\\QueryHandler" . ($subFolder ? "\\{$subFolder}" : "") . "\\{$queryName}Handler";

            $io->success([
                'Query et QueryHandler générés avec succès !',
                '',
                'Fichiers créés :',
                "- {$queryClass}",
                "- {$handlerClass}",
                '',
                'Notes :',
                '- Les Queries sont toujours SYNCHRONES',
                "- Type de retour : {$returnType}",
                '- Le QueryHandler sera automatiquement découvert',
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

    private function askQueryName(SymfonyStyle $io, InputInterface $input): ?string
    {
        $name = $input->getArgument('name');

        if (!$name) {
            $name = $io->ask('Nom de la Query (ex: GetContainer, ListWorkers)');
        }

        return $name;
    }

    private function askParameters(SymfonyStyle $io, string $context): void
    {
        $io->note([
            'Définis les paramètres de la Query',
            'Types possibles : string, int, bool, array',
            'Ou des ValueObjects/Identity existants',
        ]);

        while (true) {
            $paramName = $io->ask('Nom du paramètre (ou "stop" pour terminer)', 'stop');

            if (strtolower($paramName) === 'stop') {
                break;
            }

            $paramType = $io->ask('Type du paramètre');
            $nullable = $io->confirm('Nullable ?', false);

            $default = null;
            if ($nullable || $io->confirm('Valeur par défaut ?', false)) {
                $defaultValue = $io->ask('Valeur par défaut (laisser vide pour null)', 'null');
                $default = $defaultValue === 'null' ? null : $defaultValue;
            }

            $this->parameters[] = [
                'name' => $paramName,
                'type' => $paramType,
                'nullable' => $nullable,
                'default' => $default,
            ];

            $io->success("Paramètre '{$paramName}: {$paramType}' ajouté !");
        }
    }

    private function askDependencies(SymfonyStyle $io, string $context): array
    {
        $dependencies = [];

        $io->note([
            'Définis les dépendances du QueryHandler',
            'Exemples : Repository (lecture seule), QueryBus, Logger, etc.',
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

    private function generateQuery(string $context, string $queryName, string $subFolder): void
    {
        $dir = $this->projectDir . "/src/{$context}/Application/Query";

        if ($subFolder) {
            $dir .= "/{$subFolder}";
        }

        $this->filesystem->mkdir($dir);

        $constructorParams = $this->generateConstructorParams();

        $content = <<<PHP
<?php

namespace Marvin\\{$context}\\Application\\Query{$this->getSubFolderNamespace($subFolder)};

use Enderlab\DddCqrs\Application\Query\QueryInterface;

final readonly class {$queryName} implements QueryInterface
{
    public function __construct(
{$constructorParams}
    ) {}
}

PHP;

        $file = $dir . "/{$queryName}.php";
        $this->filesystem->dumpFile($file, $content);
    }

    private function generateQueryHandler(
        string $context,
        string $queryName,
        string $subFolder,
        string $returnType,
        array $dependencies,
        SymfonyStyle $io
    ): void {
        $dir = $this->projectDir . "/src/{$context}/Application/QueryHandler";

        if ($subFolder) {
            $dir .= "/{$subFolder}";
        }

        $this->filesystem->mkdir($dir);

        $useStatements = $this->generateHandlerUseStatements($dependencies);
        $constructorDeps = $this->generateHandlerConstructor($dependencies);
        $handlerLogic = $this->generateHandlerLogic($queryName, $returnType, $dependencies, $io);

        $content = <<<PHP
<?php

namespace Marvin\\{$context}\\Application\\QueryHandler{$this->getSubFolderNamespace($subFolder)};

use Marvin\\{$context}\\Application\\Query{$this->getSubFolderNamespace($subFolder)}\\{$queryName};
use Enderlab\DddCqrs\Application\Query\QueryHandlerInterface;
{$useStatements}

final readonly class {$queryName}Handler implements QueryHandlerInterface
{
{$constructorDeps}

    public function __invoke({$queryName} \$query): {$returnType}
    {
{$handlerLogic}
    }
}

PHP;

        $file = $dir . "/{$queryName}Handler.php";
        $this->filesystem->dumpFile($file, $content);
    }

    private function generateConstructorParams(): string
    {
        if (empty($this->parameters)) {
            return '';
        }

        $params = [];
        foreach ($this->parameters as $param) {
            $nullable = $param['nullable'] ? '?' : '';
            $default = '';

            if ($param['default'] !== null) {
                if ($param['default'] === 'null') {
                    $default = ' = null';
                } else {
                    $default = ' = ' . var_export($param['default'], true);
                }
            } elseif ($param['nullable']) {
                $default = ' = null';
            }

            $params[] = "        public {$nullable}{$param['type']} \${$param['name']}{$default},";
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

    private function generateHandlerLogic(
        string $queryName,
        string $returnType,
        array $dependencies,
        SymfonyStyle $io
    ): string {
        $io->writeln('');
        $io->note('Génération de la logique basique du QueryHandler selon le pattern détecté.');

        // Détecter le pattern
        $pattern = $this->detectPattern($queryName);

        $logic = ["        // TODO: Implémenter la logique de {$queryName}"];

        // Logique selon le pattern
        if ($pattern === 'Get' && !empty($dependencies)) {
            foreach ($dependencies as $dep) {
                if (str_contains($dep['type'], 'Repository')) {
                    $logic[] = "";
                    $logic[] = "        // Pattern Get détecté - Exemple :";
                    $logic[] = "        // \$entity = \$this->{$dep['property']}->byId(\$query->id);";
                    $logic[] = "        //";
                    $logic[] = "        // if (!\$entity) {";
                    $logic[] = "        //     throw new \\RuntimeException('Entity not found');";
                    $logic[] = "        // }";
                    $logic[] = "        //";
                    $logic[] = "        // return \$entity; // ou un DTO";
                    break;
                }
            }
        } elseif ($pattern === 'List' && !empty($dependencies)) {
            foreach ($dependencies as $dep) {
                if (str_contains($dep['type'], 'Repository')) {
                    $logic[] = "";
                    $logic[] = "        // Pattern List détecté - Exemple :";
                    $logic[] = "        // return \$this->{$dep['property']}->findAll();";
                    $logic[] = "        // ou avec pagination :";
                    $logic[] = "        // return \$this->{$dep['property']}->findBy(";
                    $logic[] = "        //     criteria: [],";
                    $logic[] = "        //     orderBy: ['createdAt' => 'DESC'],";
                    $logic[] = "        //     limit: \$query->limit,";
                    $logic[] = "        //     offset: \$query->offset";
                    $logic[] = "        // );";
                    break;
                }
            }
        } else {
            $logic[] = "";
            $logic[] = "        // Retourner des données selon le type : {$returnType}";
        }

        // Placeholder de retour
        $logic[] = "";
        if ($returnType === 'array') {
            $logic[] = "        return [];";
        } elseif ($returnType === '?array') {
            $logic[] = "        return null;";
        } elseif (str_starts_with($returnType, '?')) {
            $logic[] = "        return null;";
        } else {
            $logic[] = "        // return ...; // TODO";
        }

        return implode("\n", $logic);
    }

    private function detectPattern(string $queryName): string
    {
        if (str_starts_with($queryName, 'Get')) {
            return 'Get';
        }

        if (str_starts_with($queryName, 'List') || str_starts_with($queryName, 'Find')) {
            return 'List';
        }

        if (str_starts_with($queryName, 'Search')) {
            return 'Search';
        }

        return 'Unknown';
    }

    private function getSubFolderNamespace(string $subFolder): string
    {
        return $subFolder ? "\\{$subFolder}" : '';
    }
}
