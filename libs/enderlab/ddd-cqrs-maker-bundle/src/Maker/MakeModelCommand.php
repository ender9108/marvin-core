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
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class MakeModelCommand extends AbstractMaker
{
    private const array STANDARD_TYPES = [
        'string', 'int', 'integer', 'bool', 'boolean', 'float', 'decimal',
        'datetime', 'datetime_immutable', 'date', 'date_immutable', 'time', 'time_immutable',
        'text', 'json', 'array', 'simple_array',
    ];

    private const array RELATION_TYPES = [
        'oneToOne', 'oneToMany', 'manyToOne', 'manyToMany',
    ];

    private Filesystem $filesystem;
    private array $valueObjects = [];
    private array $fields = [];

    public function __construct(private readonly string $projectDir)
    {
        $this->filesystem = new Filesystem();
    }

    public static function getCommandName(): string
    {
        return 'make:model';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->setDescription('Crée un model DDD avec son repository et son mapping Doctrine')
            ->addArgument('context', InputArgument::OPTIONAL, 'Nom du bounded context')
            ->addArgument('model', InputArgument::OPTIONAL, 'Nom du model');
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {

        $io->title('Générateur de Model DDD');

        // 1. Demander le bounded context
        $context = $this->askContext($io, $input);
        if (!$context) {
            return;
        }

        // 2. Demander le nom du model
        $modelName = $this->askModelName($io, $input);
        if (!$modelName) {
            return;
        }

        // 3. Demander les champs
        $this->askFields($io, $context);

        // 4. Confirmer la génération
        if (!$io->confirm('Générer le model avec ces informations ?', true)) {
            $io->warning('Génération annulée');
            return;
        }

        // 5. Générer les fichiers
        try {
            $this->generateIdentityValueObject($context, $modelName);
            $this->generateIdentityDbalType($context, $modelName);
            $this->generateModel($context, $modelName);
            $this->generateRepositoryInterface($context, $modelName);
            $this->generateRepositoryOrm($context, $modelName);
            $this->generateXmlMapping($context, $modelName);

            $io->success([
                'Model généré avec succès !',
                '',
                'Fichiers créés :',
                "- Marvin\\{$context}\\Domain\\Model\\{$modelName}",
                "- Marvin\\{$context}\\Domain\\ValueObject\\Identity\\{$modelName}Id",
                "- Marvin\\{$context}\\Domain\\Repository\\{$modelName}RepositoryInterface",
                "- Marvin\\{$context}\\Infrastructure\\Persistence\\Doctrine\\ORM\\{$modelName}OrmRepository",
                "- Marvin\\{$context}\\Infrastructure\\Persistence\\Doctrine\\DBAL\\Types\\{$modelName}IdType",
                "- config/doctrine/{$context}/{$modelName}.orm.xml",
                '',
                'N\'oublie pas de :',
                "1. Enregistrer le DBAL Type dans config/packages/doctrine.yaml",
                "2. Créer les ValueObjects utilisés si besoin",
                "3. Faire une migration : php bin/console doctrine:migrations:diff",
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
                $question = new ChoiceQuestion(
                    'Choisir un bounded context existant ou en créer un nouveau',
                    array_merge($existingContexts, ['[Nouveau contexte]']),
                );
                $choice = $io->askQuestion($question);

                if ($choice === '[Nouveau contexte]') {
                    $context = $io->ask('Nom du nouveau bounded context');
                } else {
                    $context = $choice;
                }
            } else {
                $context = $io->ask('Nom du bounded context');
            }
        }

        return $context;
    }

    private function askModelName(ConsoleStyle $io, InputInterface $input): ?string
    {
        $modelName = $input->getArgument('model');

        if (!$modelName) {
            $modelName = $io->ask('Nom du model (ex: Container, Worker)');
        }

        return $modelName;
    }

    private function askFields(ConsoleStyle $io, string $context): void
    {
        $io->section('Définition des champs');

        $io->note([
            'Types disponibles :',
            '- Types standard : ' . implode(', ', self::STANDARD_TYPES),
            '- ValueObject : pour créer ou utiliser un ValueObject',
            '- Identity : pour référencer un autre model',
            '- Relations : ' . implode(', ', self::RELATION_TYPES),
        ]);

        while (true) {
            $fieldName = $io->ask('Nom du champ (ou "stop" pour terminer)', 'stop');

            if ($fieldName === 'stop') {
                break;
            }

            $fieldType = $io->ask('Type du champ');

            $field = [
                'name' => $fieldName,
                'type' => $fieldType,
                'nullable' => false,
                'default' => null,
            ];

            // Type standard
            if (in_array(strtolower($fieldType), self::STANDARD_TYPES)) {
                $field['category'] = 'standard';
                $field['nullable'] = $io->confirm('Nullable ?', false);

                if ($io->confirm('Valeur par défaut ?', false)) {
                    $field['default'] = $io->ask('Valeur par défaut');
                }
            }
            // Relation
            elseif (in_array($fieldType, self::RELATION_TYPES)) {
                $field['category'] = 'relation';
                $field['targetEntity'] = $io->ask('Entité cible (ex: Marvin\\Device\\Domain\\Model\\Device)');
                $field['nullable'] = $io->confirm('Nullable ?', false);

                if (in_array($fieldType, ['oneToMany', 'manyToMany'])) {
                    $field['mappedBy'] = $io->ask('mappedBy (optionnel)', null);
                } else {
                    $field['inversedBy'] = $io->ask('inversedBy (optionnel)', null);
                }
            }
            // ValueObject
            elseif (strtolower($fieldType) === 'valueobject') {
                $field['category'] = 'valueobject';
                $field['voName'] = $io->ask('Nom du ValueObject (ex: Status, Email)');

                $existingVo = $this->findExistingValueObject($context, $field['voName']);

                if ($existingVo) {
                    $field['voClass'] = $existingVo;
                    $io->info("ValueObject trouvé : {$existingVo}");
                } else {
                    if ($io->confirm("Le ValueObject {$field['voName']} n'existe pas. Le créer ?", true)) {
                        $voType = $io->choice('Type de ValueObject', ['Enum', 'Classe'], 'Classe');
                        $field['voType'] = strtolower($voType);

                        if ($voType === 'Enum') {
                            $field['voValues'] = $this->askEnumValues($io);
                        }

                        $this->valueObjects[] = $field;
                        $field['voClass'] = "Marvin\\{$context}\\Domain\\ValueObject\\{$field['voName']}";
                    } else {
                        $field['voClass'] = $io->ask('Classe complète du ValueObject');
                    }
                }

                $field['nullable'] = $io->confirm('Nullable ?', false);
                $field['embedded'] = $io->confirm('Embedded (objet complexe) ?', false);
            }
            // Identity
            elseif (strtolower($fieldType) === 'identity') {
                $field['category'] = 'identity';
                $field['targetModel'] = $io->ask('Model cible (ex: Container, Worker)');
                $field['targetContext'] = $io->ask('Context cible', $context);
                $field['nullable'] = $io->confirm('Nullable ?', false);
            }
            else {
                $io->warning("Type '{$fieldType}' non reconnu, considéré comme custom");
                $field['category'] = 'custom';
            }

            $this->fields[] = $field;
            $io->success("Champ '{$fieldName}' ajouté !");
        }
    }

    private function askEnumValues(ConsoleStyle $io): array
    {
        $values = [];
        $io->writeln('Définir les valeurs de l\'enum :');

        while (true) {
            $name = $io->ask('Nom de la constante (ou "stop" pour terminer)', 'stop');
            if ($name === 'stop') {
                break;
            }

            $value = $io->ask('Valeur');
            $values[$name] = $value;
        }

        return $values;
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

    private function findExistingValueObject(string $context, string $voName): ?string
    {
        $paths = [
            "Marvin\\{$context}\\Domain\\ValueObject\\{$voName}",
            "Marvin\\Shared\\Domain\\ValueObject\\{$voName}",
        ];

        foreach ($paths as $path) {
            $file = $this->projectDir . '/src/' . str_replace('\\', '/', str_replace('Marvin\\', '', $path)) . '.php';
            if (file_exists($file)) {
                return $path;
            }
        }

        return null;
    }

    private function generateIdentityValueObject(string $context, string $modelName): void
    {
        $dir = $this->projectDir . "/src/{$context}/Domain/ValueObject/Identity";
        $this->filesystem->mkdir($dir);

        $content = <<<PHP
<?php

namespace Marvin\\{$context}\\Domain\\ValueObject\\Identity;

use Ramsey\Uuid\UuidV7;

final readonly class {$modelName}Id extends UuidV7
{
    public static function generate(): self
    {
        return new self(parent::uuid7()->getBytes());
    }

    public static function fromString(string \$uuid): self
    {
        return new self(parent::fromString(\$uuid)->getBytes());
    }
}

PHP;

        $file = $dir . "/{$modelName}Id.php";
        $this->filesystem->dumpFile($file, $content);
    }

    private function generateIdentityDbalType(string $context, string $modelName): void
    {
        $dir = $this->projectDir . "/src/{$context}/Infrastructure/Persistence/Doctrine/DBAL/Types";
        $this->filesystem->mkdir($dir);

        $typeName = $this->camelCaseToSnakeCase($modelName) . '_id';

        $content = <<<PHP
<?php

namespace Marvin\\{$context}\\Infrastructure\\Persistence\\Doctrine\\DBAL\\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;
use Marvin\\{$context}\\Domain\\ValueObject\\Identity\\{$modelName}Id;

final class {$modelName}IdType extends GuidType
{
    public const NAME = '{$typeName}';

    public function getName(): string
    {
        return self::NAME;
    }

    public function convertToDatabaseValue(\$value, AbstractPlatform \$platform): ?string
    {
        if (\$value === null) {
            return null;
        }

        return \$value->toString();
    }

    public function convertToPHPValue(\$value, AbstractPlatform \$platform): ?{$modelName}Id
    {
        if (\$value === null) {
            return null;
        }

        return {$modelName}Id::fromString(\$value);
    }

    public function requiresSQLCommentHint(AbstractPlatform \$platform): bool
    {
        return true;
    }
}

PHP;

        $file = $dir . "/{$modelName}IdType.php";
        $this->filesystem->dumpFile($file, $content);
    }

    private function generateModel(string $context, string $modelName): void
    {
        $dir = $this->projectDir . "/src/{$context}/Domain/Model";
        $this->filesystem->mkdir($dir);

        $properties = $this->generateModelProperties($context, $modelName);
        $constructorParams = $this->generateConstructorParams();
        $constructorAssignments = $this->generateConstructorAssignments($modelName);
        $getters = $this->generateGetters();
        $useStatements = $this->generateUseStatements($context);

        $content = <<<PHP
<?php

namespace Marvin\\{$context}\\Domain\\Model;

use Marvin\\{$context}\\Domain\\ValueObject\\Identity\\{$modelName}Id;
use Enderlab\DddCqrs\Domain\Model\AggregateRoot;
{$useStatements}

class {$modelName} extends AggregateRoot
{
{$properties}

    public function __construct(
{$constructorParams}
    ) {
{$constructorAssignments}
    }

    public function getId(): {$modelName}Id
    {
        return \$this->id;
    }
{$getters}
}

PHP;

        $file = $dir . "/{$modelName}.php";
        $this->filesystem->dumpFile($file, $content);
    }

    private function generateModelProperties(string $context, string $modelName): string
    {
        $properties = ["    private {$modelName}Id \$id;"];

        foreach ($this->fields as $field) {
            $type = $this->getPhpType($field, $context);
            $nullable = $field['nullable'] ? '?' : '';
            $properties[] = "    private {$nullable}{$type} \${$field['name']};";
        }

        return implode("\n", $properties);
    }

    private function generateConstructorParams(): string
    {
        $params = [];

        foreach ($this->fields as $field) {
            $type = $this->getPhpType($field, '');
            $nullable = $field['nullable'] ? '?' : '';
            $default = $field['nullable'] ? ' = null' : '';

            if (isset($field['default']) && $field['default'] !== null) {
                $default = ' = ' . var_export($field['default'], true);
            }

            $params[] = "        {$nullable}{$type} \${$field['name']}{$default},";
        }

        return implode("\n", $params);
    }

    private function generateConstructorAssignments(string $modelName): string
    {
        $assignments = ["        \$this->id = {$modelName}Id::generate();"];

        foreach ($this->fields as $field) {
            $assignments[] = "        \$this->{$field['name']} = \${$field['name']};";
        }

        return implode("\n", $assignments);
    }

    private function generateUseStatements(string $context): string
    {
        $uses = [];

        foreach ($this->fields as $field) {
            if ($field['category'] === 'valueobject' && isset($field['voClass'])) {
                $uses[] = "use {$field['voClass']};";
            }
            if ($field['category'] === 'identity') {
                $targetContext = $field['targetContext'] ?? $context;
                $uses[] = "use Marvin\\{$targetContext}\\Domain\\ValueObject\\Identity\\{$field['targetModel']}Id;";
            }
            if ($field['category'] === 'relation' && isset($field['targetEntity'])) {
                $uses[] = "use {$field['targetEntity']};";
            }
        }

        return !empty($uses) ? implode("\n", array_unique($uses)) : '';
    }

    private function generateGetters(): string
    {
        $getters = [];

        foreach ($this->fields as $field) {
            $type = $this->getPhpType($field, '');
            $nullable = $field['nullable'] ? '?' : '';
            $methodName = 'get' . ucfirst($field['name']);

            $getters[] = <<<PHP

    public function {$methodName}(): {$nullable}{$type}
    {
        return \$this->{$field['name']};
    }
PHP;
        }

        return implode("\n", $getters);
    }

    private function getPhpType(array $field, string $context = ''): string
    {
        switch ($field['category']) {
            case 'standard':
                return $this->mapStandardType($field['type']);

            case 'valueobject':
                return $field['voName'];

            case 'identity':
                return $field['targetModel'] . 'Id';

            case 'relation':
                return basename(str_replace('\\', '/', $field['targetEntity']));

            default:
                return 'mixed';
        }
    }

    private function mapStandardType(string $type): string
    {
        $map = [
            'integer' => 'int',
            'boolean' => 'bool',
            'datetime' => '\DateTimeImmutable',
            'datetime_immutable' => '\DateTimeImmutable',
            'date' => '\DateTimeImmutable',
            'date_immutable' => '\DateTimeImmutable',
            'time' => '\DateTimeImmutable',
            'time_immutable' => '\DateTimeImmutable',
        ];

        return $map[$type] ?? $type;
    }

    private function generateRepositoryInterface(string $context, string $modelName): void
    {
        $dir = $this->projectDir . "/src/{$context}/Domain/Repository";
        $this->filesystem->mkdir($dir);

        $content = <<<PHP
<?php

namespace Marvin\\{$context}\\Domain\\Repository;

use Marvin\\{$context}\\Domain\\Model\\{$modelName};
use Marvin\\{$context}\\Domain\\ValueObject\\Identity\\{$modelName}Id;

interface {$modelName}RepositoryInterface
{
    public function save({$modelName} \$model): void;

    public function remove({$modelName} \$model): void;

    public function byId({$modelName}Id \$id): ?{$modelName};
}

PHP;

        $file = $dir . "/{$modelName}RepositoryInterface.php";
        $this->filesystem->dumpFile($file, $content);
    }

    private function generateRepositoryOrm(string $context, string $modelName): void
    {
        $dir = $this->projectDir . "/src/{$context}/Infrastructure/Persistence/Doctrine/ORM";
        $this->filesystem->mkdir($dir);

        $content = <<<PHP
<?php

namespace Marvin\\{$context}\\Infrastructure\\Persistence\\Doctrine\\ORM;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Marvin\\{$context}\\Domain\\Model\\{$modelName};
use Marvin\\{$context}\\Domain\\Repository\\{$modelName}RepositoryInterface;
use Marvin\\{$context}\\Domain\\ValueObject\\Identity\\{$modelName}Id;

/**
 * @extends ServiceEntityRepository<{$modelName}>
 */
class {$modelName}OrmRepository extends ServiceEntityRepository implements {$modelName}RepositoryInterface
{
    public function __construct(ManagerRegistry \$registry)
    {
        parent::__construct(\$registry, {$modelName}::class);
    }

    public function save({$modelName} \$model): void
    {
        \$this->getEntityManager()->persist(\$model);
        \$this->getEntityManager()->flush();
    }

    public function remove({$modelName} \$model): void
    {
        \$this->getEntityManager()->remove(\$model);
        \$this->getEntityManager()->flush();
    }

    public function byId({$modelName}Id \$id): ?{$modelName}
    {
        return \$this->find(\$id->toString());
    }
}

PHP;

        $file = $dir . "/{$modelName}OrmRepository.php";
        $this->filesystem->dumpFile($file, $content);
    }

    private function generateXmlMapping(string $context, string $modelName): void
    {
        $dir = $this->projectDir . "/config/doctrine/{$context}";
        $this->filesystem->mkdir($dir);

        $tableName = $this->camelCaseToSnakeCase($modelName) . 's';
        $idType = $this->camelCaseToSnakeCase($modelName) . '_id';

        $fieldsXml = [];

        foreach ($this->fields as $field) {
            $columnName = $this->camelCaseToSnakeCase($field['name']);
            $nullable = $field['nullable'] ? ' nullable="true"' : '';

            switch ($field['category']) {
                case 'standard':
                    $fieldsXml[] = "        <field name=\"{$field['name']}\" type=\"{$field['type']}\" column=\"{$columnName}\"{$nullable}/>";
                    break;

                case 'valueobject':
                    if ($field['embedded'] ?? false) {
                        $fieldsXml[] = "        <embedded name=\"{$field['name']}\" class=\"{$field['voClass']}\"/>";
                    } else {
                        $fieldsXml[] = "        <field name=\"{$field['name']}\" type=\"string\" column=\"{$columnName}\"{$nullable}/>";
                    }
                    break;

                case 'identity':
                    $targetType = $this->camelCaseToSnakeCase($field['targetModel']) . '_id';
                    $fieldsXml[] = "        <field name=\"{$field['name']}\" type=\"{$targetType}\" column=\"{$columnName}\"{$nullable}/>";
                    break;
            }
        }

        $fieldsBlock = !empty($fieldsXml) ? "\n" . implode("\n", $fieldsXml) : '';

        $content = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                  xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                  https://www.doctrine-project.org/schemas/orm/doctrine-mapping.xsd">

    <entity name="Marvin\\{$context}\\Domain\\Model\\{$modelName}" table="{$tableName}">
        <id name="id" type="{$idType}" column="id">
            <generator strategy="NONE"/>
        </id>
{$fieldsBlock}
    </entity>

</doctrine-mapping>

XML;

        $file = $dir . "/{$modelName}.orm.xml";
        $this->filesystem->dumpFile($file, $content);
    }

    private function camelCaseToSnakeCase(string $input): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
    }
}
