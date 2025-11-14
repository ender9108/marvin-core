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

class MakeValueObjectCommand extends AbstractMaker
{
    private Filesystem $filesystem;
    private array $validations = [];

    public function __construct(private readonly string $projectDir)
    {
        $this->filesystem = new Filesystem();
    }

    public static function getCommandName(): string
    {
        return 'make:value-object';
    }

    public static function getCommandDescription(): string
    {
        return 'Creates a DDD Value Object (Identity, Enum, Simple, or Complex)';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->setDescription('Crée un ValueObject (Identity, Enum, Simple ou Complex)')
            ->addArgument('context', InputArgument::OPTIONAL, 'Nom du bounded context')
            ->addArgument('name', InputArgument::OPTIONAL, 'Nom du ValueObject')
            ->addArgument('type', InputArgument::OPTIONAL, 'Type : identity, enum, simple, complex');
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
        // No external dependencies required
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $io->title('Générateur de ValueObject DDD');

        // Ask for context
        $context = $this->askContext($io, $input);
        if (!$context) {
            return;
        }

        // Ask for name
        $name = $this->askName($io, $input);
        if (!$name) {
            return;
        }

        // Ask for type
        $type = $this->askType($io, $input);

        // Generate based on type
        match ($type) {
            'identity' => $this->generateIdentityValueObject($context, $name, $io),
            'enum' => $this->generateEnumValueObject($context, $name, $io),
            'simple' => $this->generateSimpleValueObject($context, $name, $io),
            'complex' => $this->generateComplexValueObject($context, $name, $io),
            default => $io->error("Type invalide : {$type}")
        };
    }

    private function askContext(ConsoleStyle $io, InputInterface $input): ?string
    {
        $context = $input->getArgument('context');

        if (!$context) {
            $existingContexts = $this->getExistingContexts();
            $existingContexts[] = 'Shared';

            if (!empty($existingContexts)) {
                $question = new ChoiceQuestion('Choisir un bounded context', $existingContexts);
                $context = $io->askQuestion($question);
            } else {
                $context = $io->ask('Nom du bounded context (ou "Shared")', 'Shared');
            }
        }

        return $context;
    }

    private function askName(ConsoleStyle $io, InputInterface $input): ?string
    {
        $name = $input->getArgument('name');

        if (!$name) {
            $name = $io->ask('Nom du ValueObject (ex: Email, Status, Temperature)');
        }

        return $name;
    }

    private function askType(ConsoleStyle $io, InputInterface $input): string
    {
        $type = $input->getArgument('type');

        if (!$type) {
            $question = new ChoiceQuestion(
                'Type de ValueObject',
                [
                    'identity' => 'Identity (UuidV7 - ex: UserId, DeviceId)',
                    'enum' => 'Enum (énumération - ex: UserStatus, DeviceType)',
                    'simple' => 'Simple (string/int/float - ex: Email, Temperature)',
                    'complex' => 'Complex (array/collection - ex: Roles, Metadata)',
                ],
                'simple'
            );
            $type = $io->askQuestion($question);
        }

        return strtolower($type);
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

    private function generateIdentityValueObject(string $context, string $name, ConsoleStyle $io): void
    {
        $namespace = $context === 'Shared'
            ? 'Marvin\\Shared\\Domain\\ValueObject\\Identity'
            : "Marvin\\{$context}\\Domain\\ValueObject\\Identity";

        $dir = $context === 'Shared'
            ? $this->projectDir . '/src/Shared/Domain/ValueObject/Identity'
            : $this->projectDir . "/src/{$context}/Domain/ValueObject/Identity";

        $this->filesystem->mkdir($dir);

        // Generate Identity VO (simple empty class extending UuidV7)
        $content = <<<PHP
<?php

declare(strict_types=1);

namespace {$namespace};

use Symfony\Component\Uid\UuidV7;

final class {$name}Id extends UuidV7
{
}

PHP;

        $file = $dir . "/{$name}Id.php";
        $this->filesystem->dumpFile($file, $content);

        // Generate Doctrine custom type
        $this->generateDoctrineCustomType($context, $name, $namespace, $io);

        $io->success([
            "ValueObject Identity généré : {$namespace}\\{$name}Id",
            '',
            'Fichiers créés :',
            "- {$file}",
            "- Doctrine Type généré",
            '',
            'Action requise :',
            "- Ajouter le type dans config/packages/doctrine.yaml :",
            "  doctrine:",
            "    dbal:",
            "      types:",
            "        " . $this->getDoctrineTypeName($name) . ": " . $this->getDoctrineTypeClass($context, $name),
        ]);
    }

    private function generateEnumValueObject(string $context, string $name, ConsoleStyle $io): void
    {
        $namespace = $context === 'Shared'
            ? 'Marvin\\Shared\\Domain\\ValueObject'
            : "Marvin\\{$context}\\Domain\\ValueObject";

        $dir = $context === 'Shared'
            ? $this->projectDir . '/src/Shared/Domain/ValueObject'
            : $this->projectDir . "/src/{$context}/Domain/ValueObject";

        $this->filesystem->mkdir($dir);

        // Ask for enum cases
        $cases = $this->askEnumCases($io);

        if (empty($cases)) {
            $io->error('Aucune valeur définie pour l\'enum');
            return;
        }

        $casesBlock = $this->generateEnumCasesBlock($cases);

        $content = <<<PHP
<?php

declare(strict_types=1);

namespace {$namespace};

use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectTrait;
use EnderLab\ToolsBundle\Service\EnumToArrayTrait;

enum {$name}: string
{
    use ValueObjectTrait;
    use EnumToArrayTrait;

{$casesBlock}
}

PHP;

        $file = $dir . "/{$name}.php";
        $this->filesystem->dumpFile($file, $content);

        // Generate Doctrine mapping
        $this->generateDoctrineEmbeddableMapping($context, $name, 'enum', $io);

        $io->success([
            "ValueObject Enum généré : {$namespace}\\{$name}",
            '',
            'Fichiers créés :',
            "- {$file}",
            "- Doctrine mapping XML généré",
        ]);
    }

    private function generateSimpleValueObject(string $context, string $name, ConsoleStyle $io): void
    {
        $namespace = $context === 'Shared'
            ? 'Marvin\\Shared\\Domain\\ValueObject'
            : "Marvin\\{$context}\\Domain\\ValueObject";

        $dir = $context === 'Shared'
            ? $this->projectDir . '/src/Shared/Domain/ValueObject'
            : $this->projectDir . "/src/{$context}/Domain/ValueObject";

        $this->filesystem->mkdir($dir);

        // Ask for backing type
        $backingType = $io->choice(
            'Type de donnée',
            ['string', 'int', 'float', 'bool'],
            'string'
        );

        // Ask for validations
        $this->askValidations($io, $backingType);

        $validationBlock = $this->generateValidationBlock($backingType);

        $content = <<<PHP
<?php

declare(strict_types=1);

namespace {$namespace};

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectTrait;
use Stringable;

final readonly class {$name} implements Stringable
{
    use ValueObjectTrait;

    public {$backingType} \$value;

    public function __construct({$backingType} \$value)
    {
{$validationBlock}
        \$this->value = \$value;
    }

    public static function fromString({$backingType} \$value): self
    {
        return new self(\$value);
    }

    public function __toString(): string
    {
        return (string) \$this->value;
    }
}

PHP;

        $file = $dir . "/{$name}.php";
        $this->filesystem->dumpFile($file, $content);

        // Generate Doctrine mapping
        $this->generateDoctrineEmbeddableMapping($context, $name, $backingType, $io);

        $io->success([
            "ValueObject Simple généré : {$namespace}\\{$name}",
            '',
            'Fichiers créés :',
            "- {$file}",
            "- Doctrine mapping XML généré",
        ]);
    }

    private function generateComplexValueObject(string $context, string $name, ConsoleStyle $io): void
    {
        $namespace = $context === 'Shared'
            ? 'Marvin\\Shared\\Domain\\ValueObject'
            : "Marvin\\{$context}\\Domain\\ValueObject";

        $dir = $context === 'Shared'
            ? $this->projectDir . '/src/Shared/Domain/ValueObject'
            : $this->projectDir . "/src/{$context}/Domain/ValueObject";

        $this->filesystem->mkdir($dir);

        $io->note('Génération d\'un ValueObject complexe (array/collection)');

        $content = <<<PHP
<?php

declare(strict_types=1);

namespace {$namespace};

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectTrait;

final readonly class {$name}
{
    use ValueObjectTrait;

    private array \$value;

    public function __construct(array \$values = [])
    {
        // TODO: Add validation for array contents
        // Assert::allIsInstanceOf(\$values, SomeClass::class);

        \$this->value = \$values;
    }

    public static function fromArray(array \$values): self
    {
        return new self(\$values);
    }

    public function toArray(): array
    {
        return \$this->value;
    }
}

PHP;

        $file = $dir . "/{$name}.php";
        $this->filesystem->dumpFile($file, $content);

        // Generate Doctrine mapping for json type
        $this->generateDoctrineEmbeddableMapping($context, $name, 'json', $io);

        $io->success([
            "ValueObject Complex généré : {$namespace}\\{$name}",
            '',
            'Fichiers créés :',
            "- {$file}",
            "- Doctrine mapping XML généré",
            '',
            'Action requise :',
            '- Compléter la validation dans le constructeur',
            '- Ajouter des méthodes métier si nécessaire',
        ]);
    }

    private function askEnumCases(ConsoleStyle $io): array
    {
        $cases = [];
        $io->section('Définition des cases de l\'enum');
        $io->note([
            'Format : CONSTANT_NAME = \'value\'',
            'Exemple : ENABLED = \'enabled\'',
            'Tapez "stop" pour terminer',
        ]);

        while (true) {
            $constantName = $io->ask('Nom de la constante (UPPER_CASE)', 'stop');
            if (strtolower($constantName) === 'stop') {
                break;
            }

            $value = $io->ask('Valeur (lowercase)', strtolower($constantName));
            $cases[strtoupper($constantName)] = $value;

            $io->success("Case ajoutée : " . strtoupper($constantName) . " = '{$value}'");
        }

        return $cases;
    }

    private function generateEnumCasesBlock(array $cases): string
    {
        $lines = [];
        foreach ($cases as $constantName => $value) {
            $lines[] = "    case {$constantName} = '{$value}';";
        }

        return implode("\n", $lines);
    }

    private function askValidations(ConsoleStyle $io, string $backingType): void
    {
        $this->validations = [];

        $io->section('Validations');
        $io->note('Définir les règles de validation (optionnel)');

        if ($backingType === 'string') {
            if ($io->confirm('Vérifier que la valeur n\'est pas vide ?', true)) {
                $this->validations[] = ['type' => 'notEmpty'];
            }

            if ($io->confirm('Définir une longueur min/max ?', false)) {
                $min = (int) $io->ask('Longueur minimale', '1');
                $max = (int) $io->ask('Longueur maximale', '255');
                $this->validations[] = ['type' => 'lengthBetween', 'min' => $min, 'max' => $max];
            }

            if ($io->confirm('Ajouter une validation email ?', false)) {
                $this->validations[] = ['type' => 'email'];
            }

            if ($io->confirm('Ajouter une validation regex personnalisée ?', false)) {
                $pattern = $io->ask('Pattern regex');
                $this->validations[] = ['type' => 'regex', 'pattern' => $pattern];
            }
        } elseif (in_array($backingType, ['int', 'float'])) {
            if ($io->confirm('Définir une valeur min/max ?', false)) {
                $min = $io->ask('Valeur minimale');
                $max = $io->ask('Valeur maximale');
                $this->validations[] = ['type' => 'range', 'min' => $min, 'max' => $max];
            }
        }
    }

    private function generateValidationBlock(string $backingType): string
    {
        if (empty($this->validations)) {
            return "        Assert::notEmpty(\$value, '{$this->getDefaultTranslationKey()}');";
        }

        $lines = [];
        foreach ($this->validations as $validation) {
            $translationKey = $this->getDefaultTranslationKey();

            $line = match ($validation['type']) {
                'notEmpty' => "        Assert::notEmpty(\$value, '{$translationKey}');",
                'lengthBetween' => sprintf(
                    "        Assert::lengthBetween(\$value, %d, %d, '%s');",
                    $validation['min'],
                    $validation['max'],
                    $translationKey
                ),
                'email' => "        Assert::email(\$value, '{$translationKey}');",
                'regex' => "        Assert::regex(\$value, '{$validation['pattern']}', '{$translationKey}');",
                'range' => sprintf(
                    "        Assert::range(\$value, %s, %s, '%s');",
                    $validation['min'],
                    $validation['max'],
                    $translationKey
                ),
                default => "        Assert::notEmpty(\$value, '{$translationKey}');",
            };

            $lines[] = $line;
        }

        return implode("\n", $lines);
    }

    private function getDefaultTranslationKey(): string
    {
        return 'domain.exceptions.E0001.invalid_value';
    }

    private function generateDoctrineCustomType(string $context, string $name, string $namespace, ConsoleStyle $io): void
    {
        $typeDir = $context === 'Shared'
            ? $this->projectDir . '/src/Shared/Infrastructure/Persistence/Doctrine/DBAL/Types'
            : $this->projectDir . "/src/{$context}/Infrastructure/Persistence/Doctrine/DBAL/Types";

        $this->filesystem->mkdir($typeDir);

        $typeName = $this->getDoctrineTypeName($name);
        $typeClass = "{$name}IdType";

        $content = <<<PHP
<?php

declare(strict_types=1);

namespace {$this->getTypeNamespace($context)};

use {$namespace}\\{$name}Id;
use Override;
use Symfony\Bridge\Doctrine\Types\AbstractUidType;

final class {$typeClass} extends AbstractUidType
{
    #[Override]
    public function getName(): string
    {
        return '{$typeName}';
    }

    #[Override]
    protected function getUidClass(): string
    {
        return {$name}Id::class;
    }
}

PHP;

        $file = $typeDir . "/{$typeClass}.php";
        $this->filesystem->dumpFile($file, $content);
    }

    private function generateDoctrineEmbeddableMapping(string $context, string $name, string $type, ConsoleStyle $io): void
    {
        $mappingDir = $context === 'Shared'
            ? $this->projectDir . '/src/Shared/Infrastructure/Persistence/Doctrine/ORM/Mapping'
            : $this->projectDir . "/src/{$context}/Infrastructure/Persistence/Doctrine/ORM/Mapping";

        $this->filesystem->mkdir($mappingDir);

        $namespace = $context === 'Shared'
            ? 'Marvin\\Shared\\Domain\\ValueObject'
            : "Marvin\\{$context}\\Domain\\ValueObject";

        $fieldType = match ($type) {
            'enum' => "enum-type=\"{$namespace}\\{$name}\"",
            'json' => 'type="json"',
            'int' => 'type="integer"',
            'float' => 'type="float"',
            'bool' => 'type="boolean"',
            default => 'type="string" length="255"',
        };

        $content = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                  xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                          https://www.doctrine-project.org/schemas/orm/doctrine-mapping.xsd">
    <embeddable name="{$namespace}\\{$name}">
        <field name="value" {$fieldType} />
    </embeddable>
</doctrine-mapping>

XML;

        $file = $mappingDir . "/ValueObject.{$name}.orm.xml";
        $this->filesystem->dumpFile($file, $content);
    }

    private function getDoctrineTypeName(string $name): string
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $name)) . '_id';
    }

    private function getDoctrineTypeClass(string $context, string $name): string
    {
        return $this->getTypeNamespace($context) . "\\{$name}IdType";
    }

    private function getTypeNamespace(string $context): string
    {
        return $context === 'Shared'
            ? 'Marvin\\Shared\\Infrastructure\\Persistence\\Doctrine\\DBAL\\Types'
            : "Marvin\\{$context}\\Infrastructure\\Persistence\\Doctrine\\DBAL\\Types";
    }
}
