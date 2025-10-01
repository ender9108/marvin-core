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

final class MakeModel extends AbstractMaker
{
    public function __construct(private readonly string $projectDir)
    {
    }

    public static function getCommandName(): string
    {
        return 'make:model';
    }

    public static function getCommandDescription(): string
    {
        return 'Generate a Domain Model with Doctrine XML mapping and its Repository interface and Doctrine implementation.';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->addArgument('bounded-context', InputArgument::OPTIONAL, 'The Bounded Context name (e.g. Billing)')
            ->addArgument('model', InputArgument::OPTIONAL, 'The Model name (e.g. Product)')
            ->addOption('fields', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Fields in the form name:type[:length][:nullable]', [])
        ;
    }

    public function configureDependencies(DependencyBuilder $dependencies): void {
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $filesystem = new Filesystem();
        $root = rtrim($this->projectDir, DIRECTORY_SEPARATOR);

        $bc = $input->getArgument('bounded-context');
        if (!$bc) {
            $bc = $io->ask('Bounded context name (e.g. Billing)', null, function (?string $v) {
                $v = (string) $v;
                if ('' === trim($v)) {
                    throw new RuntimeException('Bounded context name cannot be empty.');
                }
                return $v;
            });
        }

        $bcNorm = $this->normalizePascal($bc);
        $srcBcDir = $root . '/src/' . $bcNorm;
        if (!is_dir($srcBcDir)) {
            throw new RuntimeException(sprintf('src/%s does not exist. It will be created with minimal structure.', $bcNorm));
        }

        $model = $input->getArgument('model');
        if (!$model) {
            $model = $io->ask('Model name (e.g. Product)', null, function (?string $v) {
                $v = (string) $v;
                if ('' === trim($v)) {
                    throw new RuntimeException('Model name cannot be empty.');
                }
                return $v;
            });
        }

        $modelNorm = $this->normalizePascal($model);

        $fieldSpecs = (array) $input->getOption('fields');
        $fields = [];
        // Broader list inspired by Symfony make:entity
        $allTypes = [
            'string','text','boolean','bool','integer','int','smallint','bigint','float','decimal',
            'datetime','datetime_immutable','datetimetz','datetimetz_immutable','date','date_immutable','time','time_immutable','dateinterval',
            'json','array','simple_array','uuid','ulid',
            'many_to_one','one_to_one','one_to_many','many_to_many',
            // camelCase aliases to match make:entity habits
            'manyToOne','oneToOne','oneToMany','manyToMany'
        ];
        if (empty($fieldSpecs)) {
            $io->writeln('Define fields. Leave name empty to finish.');
            while (true) {
                $name = $io->ask('Field name (leave empty to finish)');
                if (!$name) {
                    break;
                }
                $name = $this->normalizeFieldName($name);
                $q = new Question('Field type (autocomplete: '.implode(', ', $allTypes).')', 'string');
                $q->setAutocompleterValues($allTypes);
                $inputType = (string) $io->askQuestion($q);
                $type = $this->canonicalizeType($inputType);
                $length = null;
                $nullable = false;
                $precision = null;
                $scale = null;
                $rel = null;
                if ($type === 'string') {
                    $length = $io->ask('Length for string (default 255)', '255');
                }
                if ($type === 'decimal') {
                    $precision = (string) $io->ask('Precision for decimal (default 10)', '10');
                    $scale = (string) $io->ask('Scale for decimal (default 0)', '0');
                }
                if (in_array($type, ['string','text','int','integer','smallint','bigint','float','decimal','bool','boolean','datetime','datetime_immutable','datetimetz','datetimetz_immutable','date','date_immutable','time','time_immutable','dateinterval','json','array','simple_array','uuid','ulid','many_to_one','one_to_one'], true)) {
                    $nullable = $io->confirm('Nullable?', false);
                }
                if (in_array($type, ['many_to_one','one_to_one','one_to_many','many_to_many'], true)) {
                    $targetChoices = $this->findModelClassnames($root, $bcNorm);
                    $tq = new Question('Target model class (FQCN or short name in this BC)', null);
                    $tq->setAutocompleterValues(array_merge(array_keys($targetChoices), array_values($targetChoices)));
                    $targetInput = (string) $io->askQuestion($tq);
                    $targetFqcn = $this->resolveTargetFqcn($targetInput, $bcNorm);
                    $rel = [
                        'target' => $targetFqcn,
                    ];
                    if ($type === 'many_to_one') {
                        // owning side
                        $rel['owning'] = true;
                    } elseif ($type === 'one_to_one') {
                        $rel['owning'] = $io->confirm('Owning side?', true);
                        if (!$rel['owning']) {
                            $rel['mappedBy'] = $io->ask('mappedBy (field on target)');
                        }
                    } elseif ($type === 'one_to_many') {
                        $rel['mappedBy'] = $io->ask('mappedBy (field on target)');
                    } elseif ($type === 'many_to_many') {
                        $rel['owning'] = $io->confirm('Owning side?', true);
                        if ($rel['owning']) {
                            $rel['inversedBy'] = $io->ask('inversedBy on target (optional)', null);
                        } else {
                            $rel['mappedBy'] = $io->ask('mappedBy on target');
                        }
                    }
                }
                $fields[] = [
                    'name' => $name,
                    'type' => $type,
                    'length' => $length,
                    'precision' => $precision,
                    'scale' => $scale,
                    'nullable' => $nullable,
                    'relation' => $rel,
                ];
            }
        } else {
            foreach ($fieldSpecs as $spec) {
                // name:type[:length|precision,scale][:nullable]
                $parts = explode(':', (string) $spec);
                $name = $this->normalizeFieldName($parts[0] ?? '');
                if ($name === '') {
                    continue;
                }
                $type = $this->canonicalizeType($parts[1] ?? 'string');
                $length = null;
                $precision = null;
                $scale = null;
                if (isset($parts[2])) {
                    if ($type === 'decimal' && str_contains((string) $parts[2], ',')) {
                        [$precision, $scale] = array_map('trim', explode(',', (string) $parts[2], 2));
                    } else {
                        $length = $parts[2];
                    }
                }
                $nullable = false;
                if (isset($parts[3])) {
                    $nullable = in_array(strtolower((string) $parts[3]), ['1', 'true', 'yes', 'y'], true);
                }
                $fields[] = [
                    'name' => $name,
                    'type' => $type,
                    'length' => $length,
                    'precision' => $precision,
                    'scale' => $scale,
                    'nullable' => $nullable,
                    'relation' => null,
                ];
            }
        }

        // Prepare paths
        $modelFqcn = sprintf('Marvin\\%s\\Domain\\Model\\%s', $bcNorm, $modelNorm);
        $interfaceFqcn = sprintf('Marvin\\%s\\Domain\\Repository\\%sRepositoryInterface', $bcNorm, $modelNorm);
        $repoFqcn = sprintf('Marvin\\%s\\Infrastructure\\Persistence\\Doctrine\\ORM\\%sOrmRepository', $bcNorm, $modelNorm);

        $modelPath = sprintf('%s/src/%s/Domain/Model/%s.php', $root, $bcNorm, $modelNorm);
        $interfacePath = sprintf('%s/src/%s/Domain/Repository/%sRepositoryInterface.php', $root, $bcNorm, $modelNorm);
        $repoPath = sprintf('%s/src/%s/Infrastructure/Persistence/Doctrine/ORM/%sOrmRepository.php', $root, $bcNorm, $modelNorm);
        $xmlDir = sprintf('%s/config/doctrine/ORM/%s', $root, $bcNorm);
        $xmlPath = sprintf('%s/Model.%s.orm.xml', $xmlDir, $modelNorm);

        $filesystem->mkdir(dirname($modelPath));
        $filesystem->mkdir(dirname($interfacePath));
        $filesystem->mkdir(dirname($repoPath));
        $filesystem->mkdir($xmlDir);

        // Build Model content
        // Use a ValueObject Identity for id like Security BC (e.g., UserId).
        $useClasses = [sprintf('Marvin\\%s\\Domain\\ValueObject\\Identity\\%sId', $bcNorm, $modelNorm)];
        $props = [
            sprintf('public readonly %sId __DOLLAR__id;', $modelNorm),
        ];
        $ctorParams = [];
        $ctorInits = [];
        $needsCollections = false;
        foreach ($fields as $f) {
            $type = $f['type'];
            $rel = $f['relation'] ?? null;
            if (in_array($type, ['many_to_one','one_to_one'], true) && is_array($rel)) {
                $targetFqcn = $rel['target'];
                $short = $this->shortClass($targetFqcn);
                $useClasses[] = $targetFqcn;
                $ctorParams[] = sprintf('        private(set) %s __DOLLAR__%s,', $short, $f['name']);
            } elseif (in_array($type, ['one_to_many','many_to_many'], true) && is_array($rel)) {
                $needsCollections = true;
                $props[] = sprintf('    private Collection __DOLLAR__%s;', $f['name']);
                $ctorInits[] = sprintf('        __DOLLAR__this->%s = new ArrayCollection();', $f['name']);
            } else {
                $phpType = $this->phpTypeFor($type);
                $ctorParams[] = sprintf('private(set) %s __DOLLAR__%s,', $phpType, $f['name']);
            }
        }
        if (!empty($ctorParams)) {
            // remove trailing comma of last param when joined
            $ctorParams[count($ctorParams)-1] = rtrim($ctorParams[count($ctorParams)-1], ',');
        }
        if ($needsCollections) {
            $useClasses[] = 'Doctrine\\Common\\Collections\\Collection';
            $useClasses[] = 'Doctrine\\Common\\Collections\\ArrayCollection';
        }
        $useClasses = array_values(array_unique($useClasses));
        $useCode = '';
        foreach ($useClasses as $uc) {
            $useCode .= 'use ' . $uc . ";\n";
        }
        $ctorBodyLines = array_merge([
            sprintf('        __DOLLAR__this->id = new %sId();', $modelNorm),
        ], $ctorInits);

        $modelCode = sprintf("<?php\n\nnamespace Marvin\\%s\\Domain\\Model;\n\n%s\nfinal class %s\n{\n%s\n\n    public function __construct(\n%s\n) {\n%s\n}\n}\n", $bcNorm, $useCode, $modelNorm, $this->indentLines($props), $this->indentLines($ctorParams), implode("\n", $ctorBodyLines));

        // Build interface
        $interfaceCode = sprintf("<?php\n\nnamespace Marvin\\%s\\Domain\\Repository;\n\nuse Marvin\\%s\\Domain\\Model\\%s;\nuse Marvin\\%s\\Domain\\ValueObject\\Identity\\%sId;\n\ninterface %sRepositoryInterface\n{\n    public function save(%s __DOLLAR__model, bool __DOLLAR__flush = true): void;\n\n    public function remove(%s __DOLLAR__model, bool __DOLLAR__flush = true): void;\n\n    public function byId(%sId __DOLLAR__id): %s;\n}\n", $bcNorm, $bcNorm, $modelNorm, $bcNorm, $modelNorm, $modelNorm, $modelNorm, $modelNorm, $modelNorm, $modelNorm);

        // Build ORM repository
        $repoCode = sprintf("<?php\n\nnamespace Marvin\\%s\\Infrastructure\\Persistence\\Doctrine\\ORM;\n\nuse Doctrine\\Bundle\\DoctrineBundle\\Repository\\ServiceEntityRepository;\nuse Doctrine\\Persistence\\ManagerRegistry;\nuse Marvin\\%s\\Domain\\Exception\\%sNotFound;\nuse Marvin\\%s\\Domain\\Model\\%s;\nuse Marvin\\%s\\Domain\\Repository\\%sRepositoryInterface;\nuse Marvin\\%s\\Domain\\ValueObject\\Identity\\%sId;\nuse Override;\n\n/**\n * @extends ServiceEntityRepository<%s>\n */\nfinal class %sOrmRepository extends ServiceEntityRepository implements %sRepositoryInterface\n{\n    public function __construct(ManagerRegistry __DOLLAR__registry)\n    {\n        parent::__construct(__DOLLAR__registry, %s::class);\n    }\n\n    #[Override]\n    public function save(%s __DOLLAR__model, bool __DOLLAR__flush = true): void\n    {\n        __DOLLAR__this->getEntityManager()->persist(__DOLLAR__model);\n        if (__DOLLAR__flush) {\n            __DOLLAR__this->getEntityManager()->flush();\n        }\n    }\n\n    #[Override]\n    public function remove(%s __DOLLAR__model, bool __DOLLAR__flush = true): void\n    {\n        __DOLLAR__this->getEntityManager()->remove(__DOLLAR__model);\n        if (__DOLLAR__flush) {\n            __DOLLAR__this->getEntityManager()->flush();\n        }\n    }\n\n    #[Override]\n    public function byId(%sId __DOLLAR__id): %s\n    {\n        __DOLLAR__entity = __DOLLAR__this->findOneBy(['id' => __DOLLAR__id]);\n        if (null === __DOLLAR__entity) {\n            throw %sNotFound::withId(__DOLLAR__id);\n        }\n        return __DOLLAR__entity;\n    }\n}\n", $bcNorm, $bcNorm, $modelNorm, $bcNorm, $modelNorm, $bcNorm, $modelNorm, $bcNorm, $modelNorm, $modelNorm, $modelNorm, $modelNorm, $modelNorm, $modelNorm, $modelNorm, $modelNorm, $modelNorm, $modelNorm);

        // Build XML mapping
        $fieldsXml = [];
        foreach ($fields as $f) {
            $type = $f['type'];
            $rel = $f['relation'] ?? null;
            if (in_array($type, ['many_to_one','one_to_one','one_to_many','many_to_many'], true) && is_array($rel)) {
                $field = $f['name'];
                $target = $rel['target'];
                if ($type === 'many_to_one') {
                    $fieldsXml[] = sprintf("        <many-to-one field=\"%s\" target-entity=\"%s\">\n            <join-column%s />\n        </many-to-one>", $field, $target, $f['nullable'] ? ' nullable="true"' : '');
                } elseif ($type === 'one_to_one') {
                    if (!empty($rel['owning'])) {
                        $fieldsXml[] = sprintf("        <one-to-one field=\"%s\" target-entity=\"%s\">\n            <join-column%s />\n        </one-to-one>", $field, $target, $f['nullable'] ? ' nullable="true"' : '');
                    } else {
                        $mappedBy = (string) ($rel['mappedBy'] ?? '');
                        $fieldsXml[] = sprintf("        <one-to-one field=\"%s\" target-entity=\"%s\" mapped-by=\"%s\" />", $field, $target, $mappedBy);
                    }
                } elseif ($type === 'one_to_many') {
                    $mappedBy = (string) ($rel['mappedBy'] ?? '');
                    $fieldsXml[] = sprintf("        <one-to-many field=\"%s\" target-entity=\"%s\" mapped-by=\"%s\" />", $field, $target, $mappedBy);
                } elseif ($type === 'many_to_many') {
                    if (!empty($rel['owning'])) {
                        $inversedBy = $rel['inversedBy'] ?? null;
                        $attrs = sprintf('field="%s" target-entity="%s"', $field, $target);
                        if ($inversedBy) {
                            $attrs .= sprintf(' inversed-by="%s"', $inversedBy);
                        }
                        $joinTable = strtolower($this->tableName($bcNorm, $modelNorm) . '_' . $field);
                        $fieldsXml[] = sprintf("        <many-to-many %s>\n            <join-table name=\"%s\" />\n        </many-to-many>", $attrs, $joinTable);
                    } else {
                        $mappedBy = (string) ($rel['mappedBy'] ?? '');
                        $fieldsXml[] = sprintf("        <many-to-many field=\"%s\" target-entity=\"%s\" mapped-by=\"%s\" />", $field, $target, $mappedBy);
                    }
                }
                continue;
            }

            if ($type === 'uuid') {
                $fieldsXml[] = sprintf('        <field name="%s" type="guid" />', $f['name']);
                continue;
            }
            $attrs = [sprintf('name="%s"', $f['name']), sprintf('type="%s"', $this->doctrineTypeFor($type))];
            if ($type === 'string' && !empty($f['length'])) {
                $attrs[] = sprintf('length="%s"', (string) $f['length']);
            }
            if ($type === 'decimal') {
                if (!empty($f['precision'])) {
                    $attrs[] = sprintf('precision="%s"', (string) $f['precision']);
                }
                if (!empty($f['scale'])) {
                    $attrs[] = sprintf('scale="%s"', (string) $f['scale']);
                }
            }
            if (!empty($f['nullable'])) {
                $attrs[] = 'nullable="true"';
            }
            $fieldsXml[] = '        <field ' . implode(' ', $attrs) . ' />';
        }

        $idTypeName = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $modelNorm)) . '_id';
        $xml = sprintf("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<doctrine-mapping xmlns=\"http://doctrine-project.org/schemas/orm/doctrine-mapping\"\n                  xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"\n                  xsi:schemaLocation=\"http://doctrine-project.org/schemas/orm/doctrine-mapping\n                          https://www.doctrine-project.org/schemas/orm/doctrine-mapping.xsd\">\n    <entity\n        name=\"%s\"\n        repository-class=\"%s\"\n        table=\"%s\"\n    >\n        <id name=\"id\" type=\"%s\">\n            <generator strategy=\"NONE\" />\n        </id>\n\n%s\n    </entity>\n</doctrine-mapping>\n", $modelFqcn, $repoFqcn, $this->tableName($bcNorm, $modelNorm), $idTypeName, $this->indentLines($fieldsXml));

        // Build VO Identity class
        $voPath = sprintf('%s/src/%s/Domain/ValueObject/Identity/%sId.php', $root, $bcNorm, $modelNorm);
        $filesystem->mkdir(dirname($voPath));
        $voCode = sprintf("<?php\n\nnamespace Marvin\\%s\\Domain\\ValueObject\\Identity;\n\nuse Symfony\\Component\\Uid\\UuidV7;\n\nfinal class %sId extends UuidV7\n{\n}\n", $bcNorm, $modelNorm);

        // Build DBAL Type class
        $typeClass = sprintf('%sIdType', $modelNorm);
        $typePath = sprintf('%s/src/%s/Infrastructure/Persistence/Doctrine/DBAL/Types/%s.php', $root, $bcNorm, $typeClass);
        $filesystem->mkdir(dirname($typePath));
        $typeCode = sprintf("<?php\n\nnamespace Marvin\\%s\\Infrastructure\\Persistence\\Doctrine\\DBAL\\Types;\n\nuse Symfony\\Bridge\\Doctrine\\Types\\AbstractUidType;\nuse Marvin\\%s\\Domain\\ValueObject\\Identity\\%sId;\n\nfinal class %s extends AbstractUidType\n{\n    #[\\Override]\n    public function getName(): string\n    {\n        return '%s';\n    }\n\n    #[\\Override]\n    protected function getUidClass(): string\n    {\n        return %sId::class;\n    }\n}\n", $bcNorm, $bcNorm, $modelNorm, $typeClass, $idTypeName, $modelNorm);

        // Build NotFound Exception class
        $exceptionPath = sprintf('%s/src/%s/Domain/Exception/%sNotFound.php', $root, $bcNorm, $modelNorm);
        $bcLower = strtolower($bcNorm);
        $modelSnake = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $modelNorm));
        $exceptionCode = sprintf("<?php\n\nnamespace Marvin\\%s\\Domain\\Exception;\n\nuse EnderLab\\DddCqrsBundle\\Domain\\Exception\\DomainException;\nuse EnderLab\\DddCqrsBundle\\Domain\\Exception\\TranslatableExceptionInterface;\nuse Marvin\\%s\\Domain\\ValueObject\\Identity\\%sId;\nuse Override;\n\nfinal class %sNotFound extends DomainException implements TranslatableExceptionInterface\n{\n    public function __construct(\n        string __DOLLAR__message,\n        public readonly ?string __DOLLAR__id = null,\n    ) {\n        parent::__construct(__DOLLAR__message);\n    }\n\n    public static function withId(%sId __DOLLAR__id): self\n    {\n        return new self(\n            sprintf('%s with id %%s was not found', __DOLLAR__id->toString()),\n            __DOLLAR__id->toString(),\n        );\n    }\n\n    #[Override]\n    public function translationId(): string\n    {\n        if (null !== __DOLLAR__this->id) {\n            return '%s.exceptions.%s_not_found_with_id';\n        }\n        return '%s.exceptions.%s_not_found';\n    }\n\n    #[Override]\n    /** @return array<string, string|null> */\n    public function translationParameters(): array\n    {\n        return [\n            '%%id%%' => __DOLLAR__this->id,\n        ];\n    }\n\n    #[Override]\n    public function translationDomain(): string\n    {\n        return '%s';\n    }\n}\n", $bcNorm, $bcNorm, $modelNorm, $modelNorm, $modelNorm, $modelNorm, $bcLower, $modelSnake, $bcLower, $modelSnake, $bcLower);

        // Write files (do not overwrite if exist)
        foreach ([[$modelPath, $modelCode], [$interfacePath, $interfaceCode], [$repoPath, $repoCode], [$xmlPath, $xml], [$voPath, $voCode], [$typePath, $typeCode], [$exceptionPath, $exceptionCode]] as [$path, $code]) {
            if (file_exists($path)) {
                $io->warning(sprintf('File already exists, skipping: %s', $path));
                continue;
            }
            $code = str_replace('__DOLLAR__', '$', $code);
            $filesystem->dumpFile($path, $code);
        }

        // Update Doctrine config: register custom type and mapping section for the BC if missing
        $doctrineConfig = $root . '/config/packages/doctrine.yaml';
        if (file_exists($doctrineConfig)) {
            $yaml = file_get_contents($doctrineConfig) ?: '';
            // Register DBAL type
            if (!str_contains($yaml, $idTypeName . ': Marvin\\' . $bcNorm . '\\Infrastructure\\Persistence\\Doctrine\\DBAL\\Types\\' . $typeClass)) {
                $needle = "    orm:";
                $typeLine = sprintf("            %s: Marvin\\%s\\Infrastructure\\Persistence\\Doctrine\\DBAL\\Types\\%s\n", $idTypeName, $bcNorm, $typeClass);
                if (($pos = strpos($yaml, $needle)) !== false) {
                    $yaml = substr_replace($yaml, $typeLine . $needle, $pos, strlen($needle));
                }
            }
            // Register ORM mapping for the BC
            // Detect existing mapping by searching for the exact YAML key, e.g. "Marvin\\Security:"
            if (!str_contains($yaml, "Marvin\\" . $bcNorm . ":")) {
                $mappingsNeedle = "        controller_resolver:";
                $mappingBlock = sprintf("            Marvin\\%s:\n                type: xml\n                dir: '%%kernel.project_dir%%/config/doctrine/ORM/%s'\n                prefix: 'Marvin\\%s\\Domain'\n                is_bundle: false\n", $bcNorm, $bcNorm, $bcNorm);
                if (($pos = strpos($yaml, $mappingsNeedle)) !== false) {
                    $yaml = substr_replace($yaml, $mappingBlock . $mappingsNeedle, $pos, strlen($mappingsNeedle));
                }
            }
            file_put_contents($doctrineConfig, $yaml);
        }

        // Generate translations for NotFound exception
        $transDir = $root . '/translations';
        $filesystem->mkdir($transDir);
        $transPath = $transDir . '/' . $bcLower . '.fr.yaml';
        $notFoundKey = sprintf('%s.exceptions.%s_not_found', $bcLower, $modelSnake);
        $notFoundWithIdKey = sprintf('%s.exceptions.%s_not_found_with_id', $bcLower, $modelSnake);
        $entryNotFound = sprintf("        %s_not_found: %s introuvable.\n", $modelSnake, $modelNorm);
        $entryNotFoundWithId = sprintf("        %s_not_found_with_id: %s introuvable avec l'id \"%%id%%\".\n", $modelSnake, $modelNorm);
        if (!file_exists($transPath)) {
            $content = sprintf("%s:\n    exceptions:\n%s%s", $bcLower, $entryNotFound, $entryNotFoundWithId);
            $filesystem->dumpFile($transPath, $content);
        } else {
            $content = file_get_contents($transPath) ?: '';
            $needsNotFound = !str_contains($content, $notFoundKey);
            $needsNotFoundWithId = !str_contains($content, $notFoundWithIdKey);
            if ($needsNotFound || $needsNotFoundWithId) {
                // try to insert under existing exceptions block if present
                $exceptionsPos = strpos($content, "\n    exceptions:");
                if ($exceptionsPos !== false) {
                    $insertPos = $exceptionsPos + strlen("\n    exceptions:\n");
                    // Find line end after 'exceptions:' to insert after the newline
                    $firstNewlineAfter = strpos($content, "\n", $exceptionsPos + 1);
                    if ($firstNewlineAfter !== false) {
                        $insertPos = $firstNewlineAfter + 1;
                    }
                    $toInsert = '';
                    if ($needsNotFound) { $toInsert .= $entryNotFound; }
                    if ($needsNotFoundWithId) { $toInsert .= $entryNotFoundWithId; }
                    $content = substr($content, 0, $insertPos) . $toInsert . substr($content, $insertPos);
                } else {
                    // append a minimal exceptions block under the domain
                    if (!str_starts_with($content, $bcLower . ":")) {
                        $content .= "\n" . $bcLower . ":\n";
                    }
                    $content .= "    exceptions:\n";
                    if ($needsNotFound) { $content .= $entryNotFound; }
                    if ($needsNotFoundWithId) { $content .= $entryNotFoundWithId; }
                }
                file_put_contents($transPath, $content);
            }
        }

        $io->success(sprintf('Model %s generated in bounded context %s.', $modelNorm, $bcNorm));
        $io->writeln(sprintf(' - Model: %s', $modelPath));
        $io->writeln(sprintf(' - Repository interface: %s', $interfacePath));
        $io->writeln(sprintf(' - Doctrine repository: %s', $repoPath));
        $io->writeln(sprintf(' - XML mapping: %s', $xmlPath));
        $io->writeln(sprintf(' - VO Identity: %s', $voPath));
        $io->writeln(sprintf(' - DBAL Type: %s', $typePath));
        $io->writeln(sprintf(' - NotFound exception: %s', $exceptionPath));
        $io->writeln(sprintf(' - Translations (fr): %s', $transPath));
    }

    private function normalizePascal(string $value): string
    {
        $value = preg_replace('/[^a-z0-9]+/i', ' ', $value ?? '');
        return str_replace(' ', '', ucwords(strtolower((string) $value)));
    }

    private function normalizeFieldName(string $value): string
    {
        $value = preg_replace('/[^a-z0-9_]+/i', '_', $value ?? '');
        $value = strtolower((string) $value);
        if ($value === 'id') {
            throw new RuntimeException('The id field is generated automatically.');
        }
        return $value;
    }

    private function phpTypeFor(string $type): string
    {
        $type = $this->canonicalizeType($type);
        return match ($type) {
            'int', 'integer', 'smallint', 'bigint' => 'int',
            'float' => 'float',
            'decimal' => 'string', // avoid float precision issues
            'bool', 'boolean' => 'bool',
            'datetime', 'datetimetz', 'date', 'time', 'datetime_immutable', 'datetimetz_immutable', 'date_immutable', 'time_immutable' => '\\DateTimeInterface',
            'dateinterval' => '\\DateInterval',
            'json', 'array', 'simple_array' => 'array',
            'uuid', 'ulid', 'text', 'string' => 'string',
            default => 'string',
        };
    }

    private function doctrineTypeFor(string $type): string
    {
        $type = $this->canonicalizeType($type);
        return match ($type) {
            'int', 'integer' => 'integer',
            'smallint' => 'smallint',
            'bigint' => 'bigint',
            'float' => 'float',
            'decimal' => 'decimal',
            'bool', 'boolean' => 'boolean',
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

    private function canonicalizeType(string $type): string
    {
        $t = strtolower(trim($type));
        $map = [
            'boolean' => 'bool',
            'integer' => 'int',
            'manytoone' => 'many_to_one',
            'onetoone' => 'one_to_one',
            'onetomany' => 'one_to_many',
            'manytomany' => 'many_to_many',
        ];
        $t = str_replace(['-'], ['_'], $t);
        return $map[$t] ?? $t;
    }

    private function tableName(string $bc, string $model): string
    {
        return strtolower($bc) . '_' . strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $model));
    }

    private function indentLines(array $lines, int $level = 1): string
    {
        if (empty($lines)) {
            return '';
        }
        $indent = str_repeat(' ', 4 * $level);
        return implode("\n", array_map(static fn($l) => $indent . $l, $lines));
    }

    private function shortClass(string $fqcn): string
    {
        $pos = strrpos($fqcn, '\\');
        return false === $pos ? $fqcn : substr($fqcn, $pos + 1);
    }

    /**
     * @return array<string,string> Keys: short class name; Values: FQCN
     */
    private function findModelClassnames(string $root, string $bcNorm): array
    {
        $dir = rtrim($root, DIRECTORY_SEPARATOR) . '/src/' . $bcNorm . '/Domain/Model';
        $out = [];
        if (!is_dir($dir)) {
            return $out;
        }
        foreach (scandir($dir) ?: [] as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            if (str_ends_with($file, '.php')) {
                $short = substr($file, 0, -4);
                $out[$short] = sprintf('Marvin\\%s\\Domain\\Model\\%s', $bcNorm, $short);
            }
        }
        return $out;
    }

    private function resolveTargetFqcn(string $input, string $bcNorm): string
    {
        if (str_contains($input, '\\')) {
            return $input;
        }
        $short = $this->normalizePascal($input);
        return sprintf('Marvin\\%s\\Domain\\Model\\%s', $bcNorm, $short);
    }
}
