<?php

declare(strict_types=1);

namespace EnderLab\DddCqrsMakerBundle\Maker;

use RuntimeException;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Filesystem\Filesystem;

final class MakeApplicationCommand extends AbstractMaker
{
    public function __construct(private readonly string $projectDir)
    {
    }

    public static function getCommandName(): string
    {
        return 'make:application-command';
    }

    public static function getCommandDescription(): string
    {
        return 'Generate an Application Command (sync or async) and its Handler, with promoted properties (PHP types or ValueObjects).';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->addArgument('bounded-context', InputArgument::OPTIONAL, 'The Bounded Context name (e.g. Security)')
            ->addArgument('name', InputArgument::OPTIONAL, 'The Command class name (e.g. CreateUser)')
            ->addOption('group', null, InputOption::VALUE_REQUIRED, 'Optional sub-namespace/group under Application/Command (e.g. User)')
            ->addOption('fields', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Fields in the form name:type', [])
        ;
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $fs = new Filesystem();
        $root = rtrim($this->projectDir, DIRECTORY_SEPARATOR);

        $bc = (string) ($input->getArgument('bounded-context') ?? '');
        if ($bc === '') {
            $choices = $this->findBoundedContexts($root);
            $q = new Question('Bounded context name (e.g. Security)');
            if (!empty($choices)) {
                $q->setAutocompleterValues($choices);
                if (method_exists($q, 'setAutocompleterCallback')) {
                    $q->setAutocompleterCallback(function (string $userInput) use ($choices): array {
                        $u = strtolower($userInput);
                        return array_values(array_filter($choices, static function (string $c) use ($u): bool {
                            return $u === '' || str_contains(strtolower($c), $u);
                        }));
                    });
                }
            }
            $q->setValidator(function (?string $v) {
                $v = (string) $v;
                if ('' === trim($v)) { throw new RuntimeException('Bounded context name cannot be empty.'); }
                return $v;
            });
            /** @var string $bc */
            $bc = (string) $io->askQuestion($q);
        }
        $bcNorm = $this->normalizePascal($bc);
        $srcBcDir = $root . '/src/' . $bcNorm;
        if (!is_dir($srcBcDir)) {
            throw new RuntimeException(sprintf('src/%s does not exist. Create the bounded context first.', $bcNorm));
        }

        $name = (string) ($input->getArgument('name') ?? '');
        if ($name === '') {
            $name = (string) $io->ask('Command class name (e.g. CreateUser)', null, function (?string $v) {
                $v = (string) $v; if ('' === trim($v)) { throw new RuntimeException('Name cannot be empty.'); }
                return $v;
            });
        }
        $nameNorm = $this->normalizePascal($name);

        $group = (string) ($input->getOption('group') ?? '');
        if ($group === '') {
            $group = (string) $io->ask('Optional group/sub-namespace (e.g. User). Leave empty to skip', '');
        }
        $groupNorm = trim($group) !== '' ? $this->normalizePascal($group) : '';

        // Discover available ValueObjects limited to selected BC and Shared (including Identity)
        $voMap = $this->findValueObjectClasses($root, $bcNorm); // [short => FQCN]
        $voAutocomplete = array_merge(array_keys($voMap), array_values($voMap));
        $phpTypes = ['string','int','integer','float','decimal','bool','boolean','array','json','datetime','datetime_immutable','date','time','uuid','ulid'];
        $typeChoices = array_values(array_unique(array_merge($phpTypes, $voAutocomplete)));

        $fieldSpecs = (array) $input->getOption('fields');
        $fields = [];
        if (empty($fieldSpecs)) {
            $io->writeln('Define command properties. Leave name empty to finish.');
            while (true) {
                $fname = (string) $io->ask('Property name (leave empty to finish)');
                if ($fname === '') { break; }
                $fname = $this->normalizeFieldName($fname);
                $q = new Question('Property type', 'string');
                $q->setAutocompleterValues($typeChoices);
                $inputType = (string) $io->askQuestion($q);
                $inputTypeTrim = trim($inputType);
                $normShort = $this->normalizePascal($inputTypeTrim);
                if (isset($voMap[$normShort])) {
                    $type = $voMap[$normShort]; // FQCN VO
                } elseif (in_array($inputTypeTrim, $voMap, true)) {
                    $type = $inputTypeTrim; // already FQCN
                } else {
                    $type = $this->canonicalizeType($inputTypeTrim);
                }
                $nullable = $io->confirm('Is this property nullable?', false);
                $fields[] = [
                    'name' => $fname,
                    'type' => $type,
                    'nullable' => $nullable,
                ];
            }
        } else {
            foreach ($fieldSpecs as $spec) {
                $parts = explode(':', (string) $spec);
                $fname = $this->normalizeFieldName($parts[0] ?? '');
                if ($fname === '') { continue; }
                $rawType = (string) ($parts[1] ?? 'string');
                $normShort = $this->normalizePascal($rawType);
                if (isset($voMap[$normShort])) {
                    $type = $voMap[$normShort];
                } elseif (in_array($rawType, $voMap, true)) {
                    $type = $rawType;
                } else {
                    $type = $this->canonicalizeType($rawType);
                }
                $fields[] = [ 'name' => $fname, 'type' => $type, 'nullable' => false ];
            }
        }

        // Ask sync or async
        $isSync = $io->confirm('Is this a synchronous command? (Yes = SyncCommandInterface, No = CommandInterface)', true);
        $commandInterfaceFqcn = $isSync
            ? 'EnderLab\\DddCqrsBundle\\Application\\Command\\SyncCommandInterface'
            : 'EnderLab\\DddCqrsBundle\\Application\\Command\\CommandInterface';
        $handlerInterfaceFqcn = $isSync
            ? 'EnderLab\\DddCqrsBundle\\Application\\Command\\SyncCommandHandlerInterface'
            : 'EnderLab\\DddCqrsBundle\\Application\\Command\\CommandHandlerInterface';
        $commandInterfaceShort = $isSync ? 'SyncCommandInterface' : 'CommandInterface';
        $handlerInterfaceShort = $isSync ? 'SyncCommandHandlerInterface' : 'CommandHandlerInterface';

        // Prepare paths & namespaces
        $cmdNs = sprintf('Marvin\\%s\\Application\\Command', $bcNorm);
        $handlerNs = sprintf('Marvin\\%s\\Application\\CommandHandler', $bcNorm);
        if ($groupNorm !== '') {
            $cmdNs .= '\\' . $groupNorm;
            $handlerNs .= '\\' . $groupNorm;
        }
        $cmdPath = sprintf('%s/src/%s/Application/Command%s/%s.php', $root, $bcNorm, $groupNorm ? '/' . $groupNorm : '', $nameNorm);
        $handlerPath = sprintf('%s/src/%s/Application/CommandHandler%s/%sHandler.php', $root, $bcNorm, $groupNorm ? '/' . $groupNorm : '', $nameNorm);

        $fs->mkdir(dirname($cmdPath));
        $fs->mkdir(dirname($handlerPath));

        // Build Command class code
        $use = [$commandInterfaceFqcn];
        $props = [];
        $newVoUses = [];
        foreach ($fields as $f) {
            $type = $f['type'];
            $nullable = !empty($f['nullable']);
            $voFqcns = array_flip(array_values($voMap));
            if (isset($voFqcns[$type])) {
                $use[] = $type;
                $newVoUses[] = $type;
                $short = $this->shortClass($type);
                $typeDecl = $nullable ? ('?' . $short) : $short;
                $default = $nullable ? ' = null' : '';
                $props[] = sprintf('public %s $%s%s,', $typeDecl, $f['name'], $default);
            } else {
                $phpType = $this->phpTypeFor($type);
                $typeDecl = $nullable ? ('?' . $phpType) : $phpType;
                $default = $nullable ? ' = null' : '';
                $props[] = sprintf('public %s $%s%s,', $typeDecl, $f['name'], $default);
            }
        }
        if (!empty($props)) {
            $props[count($props)-1] = rtrim($props[count($props)-1], ',');
        }
        sort($use);
        $useCode = '';
        foreach (array_unique($use) as $u) { $useCode .= 'use ' . $u . ";\n"; }

        $cmdCode = sprintf("<?php\n\nnamespace %s;\n\n%s\nfinal readonly class %s implements %s\n{\n    public function __construct(\n%s\n    ) {\n    }\n}\n",
            $cmdNs,
            $useCode,
            $nameNorm,
            $commandInterfaceShort,
            $this->indentLines($props, 2)
        );

        // Build Handler class code (skeleton)
        $useH = [
            $cmdNs . '\\' . $nameNorm,
            $handlerInterfaceFqcn,
            'Symfony\\Component\\Messenger\\Attribute\\AsMessageHandler',
        ];
        $useCodeH = '';
        foreach ($useH as $u) { $useCodeH .= 'use ' . $u . ";\n"; }

        $handlerCode = sprintf("<?php\n\nnamespace %s;\n\n%s\n#[AsMessageHandler]\nfinal readonly class %sHandler implements %s\n{\n    public function __construct() {\n    }\n\n    public function __invoke(%s __DOLLAR__command): void\n    {\n        // TODO: implement handler logic\n    }\n}\n",
            $handlerNs,
            $useCodeH,
            $nameNorm,
            $handlerInterfaceShort,
            $nameNorm
        );

        // If command already exists, inject new properties into existing constructor and add missing use statements
        if (file_exists($cmdPath)) {
            $content = file_get_contents($cmdPath) ?: '';
            // Add missing use statements for VO types
            foreach (array_unique($newVoUses) as $fqcn) {
                if ($fqcn === '') { continue; }
                if (!str_contains($content, 'use ' . $fqcn . ';')) {
                    // Find last use; if none, place after namespace declaration
                    $nsDeclEnd = strpos($content, ";\n");
                    if ($nsDeclEnd !== false) {
                        // find last use line after namespace
                        $lastUsePos = strrpos($content, "\nuse ");
                        if ($lastUsePos !== false) {
                            $semiPos = strpos($content, ";\n", $lastUsePos);
                            if ($semiPos !== false) {
                                $insertPos = $semiPos + 2;
                                $content = substr($content, 0, $insertPos) . 'use ' . $fqcn . ";\n" . substr($content, $insertPos);
                            }
                        } else {
                            // insert right after namespace block blank line
                            $content = substr($content, 0, $nsDeclEnd + 2) . "\nuse " . $fqcn . ";\n" . substr($content, $nsDeclEnd + 2);
                        }
                    }
                }
            }
            // Inject constructor params
            $marker = 'public function __construct(';
            $start = strpos($content, $marker);
            if ($start === false) {
                $io->warning('Could not locate constructor to append properties. Skipping update for command class.');
            } else {
                $openParenPos = $start + strlen($marker);
                $closePattern = "\n    ) {";
                $end = strpos($content, $closePattern, $openParenPos);
                if ($end === false) {
                    $io->warning('Could not locate end of constructor parameter list. Skipping update for command class.');
                } else {
                    $existingParams = substr($content, $openParenPos, $end - $openParenPos);
                    $insertion = $this->indentLines($props, 2);
                    $existingParamsTrim = rtrim($existingParams);
                    if ($existingParamsTrim !== '') {
                        // ensure existing ends with comma
                        if (!preg_match('/,\s*$/', $existingParamsTrim)) {
                            $existingParamsTrim .= ",\n";
                        } else {
                            $existingParamsTrim .= "\n";
                        }
                    }
                    $newParamsBlock = $existingParamsTrim . $insertion;
                    $content = substr($content, 0, $openParenPos) . $newParamsBlock . substr($content, $end);
                }
            }
            file_put_contents($cmdPath, $content);
        } else {
            // Create new command file
            $fs->dumpFile($cmdPath, $cmdCode);
        }

        // Create handler if not exists
        if (!file_exists($handlerPath)) {
            $code = str_replace('__DOLLAR__', '$', $handlerCode);
            $fs->dumpFile($handlerPath, $code);
        } else {
            $io->warning(sprintf('File already exists, skipping: %s', $handlerPath));
        }

        $io->success(sprintf('Application Command %s generated/updated in bounded context %s.', $nameNorm, $bcNorm));
        $io->writeln(sprintf(' - Command: %s', $cmdPath));
        $io->writeln(sprintf(' - Handler: %s', $handlerPath));
    }

    private function normalizePascal(string $value): string
    {
        $value = (string) $value;
        $value = preg_replace('/[^a-zA-Z0-9]+/', ' ', $value) ?? '';
        $parts = preg_split('/\s+/', trim($value)) ?: [];
        $out = '';
        foreach ($parts as $p) {
            if ($p === '') { continue; }
            $out .= strtoupper(substr($p, 0, 1)) . substr($p, 1);
        }
        return $out;
    }

    private function normalizeFieldName(string $value): string
    {
        $value = (string) $value;
        // Keep user-provided casing (e.g., parentZone), just sanitize invalid characters
        $value = preg_replace('/[^a-zA-Z0-9_]+/', '_', $value) ?? '';
        // Ensure it doesn't start with a digit
        if ($value !== '' && ctype_digit($value[0])) {
            $value = '_' . $value;
        }
        return $value;
    }

    private function canonicalizeType(string $type): string
    {
        if (str_contains($type, '\\')) { return trim($type); }
        $t = strtolower(trim($type));
        return match ($t) {
            'boolean' => 'bool',
            'integer' => 'int',
            default => $t,
        };
    }

    private function phpTypeFor(string $type): string
    {
        $type = $this->canonicalizeType($type);
        return match ($type) {
            'int','integer' => 'int',
            'float' => 'float',
            'decimal' => 'string',
            'bool','boolean' => 'bool',
            'datetime','datetimetz','date','time','datetime_immutable','datetimetz_immutable','date_immutable','time_immutable' => '\\DateTimeInterface',
            'dateinterval' => '\\DateInterval',
            'json','array','simple_array' => 'array',
            'uuid','ulid','text','string' => 'string',
            default => 'string',
        };
    }

    /**
     * @return array<string,string> Keys: short class name; Values: FQCN
     */
    private function findValueObjectClasses(string $root, string $bcNorm): array
    {
        $base = rtrim($root, DIRECTORY_SEPARATOR) . '/src';
        $result = [];
        if (!is_dir($base)) { return $result; }

        // Limit to selected BC and Shared only
        $targets = [];
        $targets['Marvin\\' . $bcNorm . '\\Domain\\ValueObject'] = $base . '/' . $bcNorm . '/Domain/ValueObject';
        $targets['Marvin\\Shared\\Domain\\ValueObject'] = $base . '/Shared/Domain/ValueObject';

        foreach ($targets as $nsPrefix => $voDir) {
            if (!is_dir($voDir)) { continue; }
            $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($voDir, \FilesystemIterator::SKIP_DOTS));
            foreach ($it as $fileInfo) {
                if (!$fileInfo->isFile()) { continue; }
                $filePath = (string) $fileInfo->getPathname();
                if (!str_ends_with($filePath, '.php')) { continue; }
                $short = basename($filePath, '.php');

                // Detect Identity sub-namespace
                $isIdentity = str_contains($filePath, DIRECTORY_SEPARATOR . 'Identity' . DIRECTORY_SEPARATOR);
                $content = file_get_contents($filePath) ?: '';

                // Include Identity classes unconditionally; for others, require ValueObjectInterface
                if (!$isIdentity && !str_contains($content, 'ValueObjectInterface')) { continue; }

                // Build FQCN by converting file subpath to namespace under the nsPrefix
                $subPath = trim(str_replace($voDir, '', dirname($filePath)), DIRECTORY_SEPARATOR);
                $subNs = $subPath !== '' ? ('\\' . str_replace(DIRECTORY_SEPARATOR, '\\', $subPath)) : '';
                $fqcn = $nsPrefix . $subNs . '\\' . $short;

                $result[$short] = $fqcn;
            }
        }

        ksort($result);
        return $result;
    }

    private function indentLines(array $lines, int $level = 1): string
    {
        if (empty($lines)) { return ''; }
        $indent = str_repeat(' ', 4 * $level);
        return implode("\n", array_map(static fn($l) => $indent . $l, $lines));
    }

    private function shortClass(string $fqcn): string
    {
        $pos = strrpos($fqcn, '\\');
        return false === $pos ? $fqcn : substr($fqcn, $pos + 1);
    }

    /**
     * Return first-level directories under src as bounded contexts.
     * @return string[]
     */
    private function findBoundedContexts(string $root): array
    {
        $base = rtrim($root, DIRECTORY_SEPARATOR) . '/src';
        $out = [];
        if (!is_dir($base)) { return $out; }
        foreach (scandir($base) ?: [] as $entry) {
            if ($entry === '.' || $entry === '..') { continue; }
            if (is_dir($base . '/' . $entry)) {
                $out[] = $entry;
            }
        }
        sort($out, SORT_NATURAL);
        return $out;
    }
}
