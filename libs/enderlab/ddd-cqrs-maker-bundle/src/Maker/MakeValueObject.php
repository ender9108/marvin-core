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

final class MakeValueObject extends AbstractMaker
{
    public function __construct(private readonly string $projectDir) {}

    public static function getCommandName(): string
    {
        return 'make:value-object';
    }

    public static function getCommandDescription(): string
    {
        return 'Generate a ValueObject class and its Doctrine XML embeddable mapping.';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->addArgument('bounded-context', InputArgument::OPTIONAL, 'The Bounded Context name (e.g. Security)')
            ->addArgument('name', InputArgument::OPTIONAL, 'The ValueObject name (e.g. Firstname)')
            ->addOption('type', null, InputOption::VALUE_REQUIRED, 'Value field type (e.g. string, int, array, datetime, ...)')
            ->addOption('length', null, InputOption::VALUE_OPTIONAL, 'String length (for string type)')
            ->addOption('precision', null, InputOption::VALUE_OPTIONAL, 'Decimal precision')
            ->addOption('scale', null, InputOption::VALUE_OPTIONAL, 'Decimal scale')
        ;
    }

    public function configureDependencies(DependencyBuilder $dependencies): void {}

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $fs = new Filesystem();
        $root = rtrim($this->projectDir, DIRECTORY_SEPARATOR);

        $bc = $input->getArgument('bounded-context');
        if (!$bc) {
            $bc = $io->ask('Bounded context name (e.g. Security)', null, function (?string $v) {
                $v = (string) $v;
                if ('' === trim($v)) { throw new RuntimeException('Bounded context name cannot be empty.'); }
                return $v;
            });
        }
        $bcNorm = $this->normalizePascal((string) $bc);
        $srcBcDir = $root . '/src/' . $bcNorm;
        if (!is_dir($srcBcDir)) {
            throw new RuntimeException(sprintf('src/%s does not exist. Create the bounded context first.', $bcNorm));
        }

        $name = $input->getArgument('name');
        if (!$name) {
            $name = $io->ask('ValueObject name (e.g. Firstname)', null, function (?string $v) {
                $v = (string) $v; if ('' === trim($v)) { throw new RuntimeException('Name cannot be empty.'); }
                return $v;
            });
        }
        $nameNorm = $this->normalizePascal((string) $name);

        // Ask type
        $type = (string) ($input->getOption('type') ?? '');
        $allTypes = [
            'string','text','boolean','bool','integer','int','smallint','bigint','float','decimal',
            'datetime','datetime_immutable','datetimetz','datetimetz_immutable','date','date_immutable',
            'time','time_immutable','dateinterval','json','array','simple_array','uuid','ulid'
        ];
        if ($type === '') {
            $q = new Question('Value type', 'string');
            $q->setAutocompleterValues($allTypes);
            $type = (string) $io->askQuestion($q);
        }
        $type = $this->canonicalizeType($type);

        $length = (string) ($input->getOption('length') ?? '');
        $precision = (string) ($input->getOption('precision') ?? '');
        $scale = (string) ($input->getOption('scale') ?? '');
        if ($type === 'string' && $length === '') {
            $length = (string) $io->ask('Length for string (default 255)', '255');
        }
        if ($type === 'decimal') {
            $precision = (string) ($precision ?: $io->ask('Precision for decimal (default 10)', '10'));
            $scale = (string) ($scale ?: $io->ask('Scale for decimal (default 0)', '0'));
        }

        // Decide interface and PHP type
        [$interfaceFqcn, $phpType, $needsArrayApi, $needsToString] = $this->interfaceAndPhpTypeFor($type);

        // Build class code
        $ns = sprintf('Marvin\\%s\\Domain\\ValueObject', $bcNorm);
        $classPath = sprintf('%s/src/%s/Domain/ValueObject/%s.php', $root, $bcNorm, $nameNorm);
        $fs->mkdir(dirname($classPath));

        $use = [
            $interfaceFqcn,
        ];
        $implements = [$this->short($interfaceFqcn)];
        if ($needsToString) { $use[] = 'Stringable'; $implements[] = 'Stringable'; }
        $use = array_values(array_unique($use));

        $useCode = '';
        foreach ($use as $u) { $useCode .= 'use ' . $u . ";\n"; }

        $propType = $this->phpScalarFor($phpType);
        $ctorTypeHint = $propType ? ($propType . ' ') : '';
        $ctor = "    public function __construct(" . $ctorTypeHint . "\$value)\n    {\n        \$this->value = \$value;\n    }\n";

        $methods = [];
        if ($needsToString) {
            $methods[] = "    public function __toString(): string\n    {\n        return (string) \$this->value;\n    }\n";
        }
        if ($needsArrayApi) {
            $methods[] = "    public function toArray(): array\n    {\n        return \$this->value;\n    }\n";
            $methods[] = sprintf("    public static function fromArray(array \$data): self\n    {\n        return new self(\$data);\n    }\n");
        }

        $classCode = sprintf("<?php\n\nnamespace %s;\n\n%s\nfinal readonly class %s implements %s\n{\n    private %s \$value;\n\n%s\n%s}\n",
            $ns,
            $useCode,
            $nameNorm,
            implode(', ', $implements),
            $propType ?: 'mixed',
            $ctor,
            implode("\n", $methods)
        );

        // Build XML mapping
        $xmlDir = sprintf('%s/config/doctrine/ORM/%s', $root, $bcNorm);
        $xmlPath = sprintf('%s/ValueObject.%s.orm.xml', $xmlDir, $nameNorm);
        $fs->mkdir($xmlDir);

        $doctrineType = $this->doctrineTypeFor($type);
        $attrs = [];
        if ($doctrineType !== 'string') {
            $attrs[] = sprintf('type="%s"', $doctrineType);
        }
        if ($type === 'string' && $length !== '') {
            $attrs[] = sprintf('length="%s"', $length);
        }
        if ($type === 'decimal') {
            if ($precision !== '') { $attrs[] = sprintf('precision="%s"', $precision); }
            if ($scale !== '') { $attrs[] = sprintf('scale="%s"', $scale); }
        }
        if ($type === 'array' || $type === 'json' || $type === 'simple_array') {
            // convention: use json
            $attrs = ['type="json"'];
        }

        $xml = sprintf("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<doctrine-mapping xmlns=\"http://doctrine-project.org/schemas/orm/doctrine-mapping\"\n                  xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"\n                  xsi:schemaLocation=\"http://doctrine-project.org/schemas/orm/doctrine-mapping\n                          https://www.doctrine-project.org/schemas/orm/doctrine-mapping.xsd\"\n>\n    <embeddable name=\"%s\">\n        <field name=\"value\" %s/>\n    </embeddable>\n</doctrine-mapping>\n",
            sprintf('Marvin\\%s\\Domain\\ValueObject\\%s', $bcNorm, $nameNorm),
            empty($attrs) ? '' : implode(' ', $attrs)
        );

        // Write files
        foreach ([[ $classPath, $classCode ], [ $xmlPath, $xml ]] as [$path, $code]) {
            if (file_exists($path)) { $io->warning(sprintf('File already exists, skipping: %s', $path)); continue; }
            $fs->dumpFile($path, $code);
        }

        // Ensure doctrine mapping section for BC exists
        $doctrineConfig = $root . '/config/packages/doctrine.yaml';
        if (file_exists($doctrineConfig)) {
            $yaml = file_get_contents($doctrineConfig) ?: '';
            if (!str_contains($yaml, "Marvin\\" . $bcNorm . ":")) {
                $needle = "        controller_resolver:"; // heuristic like in make:model
                $block = sprintf("            Marvin\\%s:\n                type: xml\n                dir: '%%kernel.project_dir%%/config/doctrine/ORM/%s'\n                prefix: 'Marvin\\%s\\Domain'\n                is_bundle: false\n", $bcNorm, $bcNorm, $bcNorm);
                if (($pos = strpos($yaml, $needle)) !== false) {
                    $yaml = substr_replace($yaml, $block . $needle, $pos, strlen($needle));
                    file_put_contents($doctrineConfig, $yaml);
                }
            }
        }

        $io->success(sprintf('ValueObject %s generated in bounded context %s.', $nameNorm, $bcNorm));
        $io->writeln(sprintf(' - Class: %s', $classPath));
        $io->writeln(sprintf(' - XML mapping: %s', $xmlPath));
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

    private function canonicalizeType(string $type): string
    {
        $t = strtolower(trim($type));
        return match ($t) {
            'boolean' => 'bool',
            'integer' => 'int',
            default => $t,
        };
    }

    private function doctrineTypeFor(string $type): string
    {
        return match ($type) {
            'int' => 'integer',
            'smallint' => 'smallint',
            'bigint' => 'bigint',
            'float' => 'float',
            'decimal' => 'decimal',
            'bool' => 'boolean',
            'datetime' => 'datetime',
            'datetimetz' => 'datetimetz',
            'datetime_immutable' => 'datetime_immutable',
            'datetimetz_immutable' => 'datetimetz_immutable',
            'date' => 'date',
            'date_immutable' => 'date_immutable',
            'time' => 'time',
            'time_immutable' => 'time_immutable',
            'dateinterval' => 'dateinterval',
            'json' => 'json',
            'array' => 'array',
            'simple_array' => 'simple_array',
            'uuid' => 'guid',
            'ulid' => 'ulid',
            'text' => 'text',
            default => 'string',
        };
    }

    private function interfaceAndPhpTypeFor(string $type): array
    {
        // returns [interfaceFqcn, phpType, needsArrayApi(bool), needsToString(bool)]
        $isDatetime = in_array($type, [
            'datetime','datetimetz','datetime_immutable','datetimetz_immutable','date','date_immutable','time','time_immutable','dateinterval'
        ], true);
        if ($type === 'array' || $type === 'json' || $type === 'simple_array') {
            return ['Marvin\\Shared\\Domain\\ValueObject\\ArrayValueObjectInterface', 'array', true, false];
        }
        if ($isDatetime) {
            return ['Marvin\\Shared\\Domain\\ValueObject\\DatetimeValueObjectInterface', '\\DateTimeInterface', false, true];
        }
        // default scalar mapping
        return ['EnderLab\\DddCqrsBundle\\Domain\\ValueObject\\ValueObjectInterface', match ($type) {
            'int','smallint','bigint' => 'int',
            'float','decimal' => 'string', // string for decimal to avoid float issues
            'bool' => 'bool',
            'text','string','uuid','ulid' => 'string',
            default => 'string',
        }, false, true];
    }

    private function phpScalarFor(string $t): string
    {
        return match ($t) {
            'int','float','string','bool','array','\\DateTimeInterface' => $t,
            default => '',
        };
    }

    private function short(string $fqcn): string
    {
        $p = strrpos($fqcn, '\\');
        return false === $p ? $fqcn : substr($fqcn, $p+1);
    }
}
