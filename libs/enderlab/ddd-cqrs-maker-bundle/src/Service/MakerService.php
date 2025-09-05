<?php

namespace EnderLab\DddCqrsMakerBundle\Service;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\DBAL\Types\Type;
use EnderLab\BlameableBundle\Infrastructure\Interface\BlameableInterface;
use EnderLab\DddCqrsBundle\Domain\Model\AggregateRoot;
use EnderLab\TimestampableBundle\Interface\TimestampableInterface;
use Exception;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\Doctrine\EntityRelation;
use Symfony\Bundle\MakerBundle\Exception\RuntimeCommandException;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Util\ClassDetails;
use Symfony\Bundle\MakerBundle\Util\ClassSourceManipulator;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use function Symfony\Component\String\u;

class MakerService
{
    private const array DOMAIN_FOLDERS = [
        '%s'.DIRECTORY_SEPARATOR.'Application',
        '%s'.DIRECTORY_SEPARATOR.'Application'.DIRECTORY_SEPARATOR.'Command',
        '%s'.DIRECTORY_SEPARATOR.'Application'.DIRECTORY_SEPARATOR.'CommandHandler',
        '%s'.DIRECTORY_SEPARATOR.'Application'.DIRECTORY_SEPARATOR.'Event',
        '%s'.DIRECTORY_SEPARATOR.'Application'.DIRECTORY_SEPARATOR.'EventHandler',
        '%s'.DIRECTORY_SEPARATOR.'Application'.DIRECTORY_SEPARATOR.'Query',
        '%s'.DIRECTORY_SEPARATOR.'Application'.DIRECTORY_SEPARATOR.'QueryHandler',
        '%s'.DIRECTORY_SEPARATOR.'Domain',
        '%s'.DIRECTORY_SEPARATOR.'Domain'.DIRECTORY_SEPARATOR.'Event',
        '%s'.DIRECTORY_SEPARATOR.'Domain'.DIRECTORY_SEPARATOR.'Exception',
        '%s'.DIRECTORY_SEPARATOR.'Domain'.DIRECTORY_SEPARATOR.'Model',
        '%s'.DIRECTORY_SEPARATOR.'Domain'.DIRECTORY_SEPARATOR.'Repository',
        '%s'.DIRECTORY_SEPARATOR.'Domain'.DIRECTORY_SEPARATOR.'ValueObject',
        '%s'.DIRECTORY_SEPARATOR.'Infrastructure',
        '%s'.DIRECTORY_SEPARATOR.'Infrastructure'.DIRECTORY_SEPARATOR.'Persistence',
        '%s'.DIRECTORY_SEPARATOR.'Infrastructure'.DIRECTORY_SEPARATOR.'Persistence'.DIRECTORY_SEPARATOR.'Doctrine',
        '%s'.DIRECTORY_SEPARATOR.'Infrastructure'.DIRECTORY_SEPARATOR.'Persistence'.DIRECTORY_SEPARATOR.'Doctrine'.DIRECTORY_SEPARATOR.'ORM',
        '%s'.DIRECTORY_SEPARATOR.'Infrastructure'.DIRECTORY_SEPARATOR.'Persistence'.DIRECTORY_SEPARATOR.'Doctrine'.DIRECTORY_SEPARATOR.'DBAL',
        '%s'.DIRECTORY_SEPARATOR.'Infrastructure'.DIRECTORY_SEPARATOR.'Persistence'.DIRECTORY_SEPARATOR.'Doctrine'.DIRECTORY_SEPARATOR.'DBAL'.DIRECTORY_SEPARATOR.'Types',
        '%s'.DIRECTORY_SEPARATOR.'Infrastructure'.DIRECTORY_SEPARATOR.'DataFixtures',
        '%s'.DIRECTORY_SEPARATOR.'Infrastructure'.DIRECTORY_SEPARATOR.'Framework',
        '%s'.DIRECTORY_SEPARATOR.'Infrastructure'.DIRECTORY_SEPARATOR.'Framework'.DIRECTORY_SEPARATOR.'Symfony',
        '%s'.DIRECTORY_SEPARATOR.'Infrastructure'.DIRECTORY_SEPARATOR.'Framework'.DIRECTORY_SEPARATOR.'ApiPlatform',
        '%s'.DIRECTORY_SEPARATOR.'Infrastructure'.DIRECTORY_SEPARATOR.'Framework'.DIRECTORY_SEPARATOR.'ApiPlatform'.DIRECTORY_SEPARATOR.'State',
        '%s'.DIRECTORY_SEPARATOR.'Infrastructure'.DIRECTORY_SEPARATOR.'Framework'.DIRECTORY_SEPARATOR.'ApiPlatform'.DIRECTORY_SEPARATOR.'State'.DIRECTORY_SEPARATOR.'Processor',
        '%s'.DIRECTORY_SEPARATOR.'Infrastructure'.DIRECTORY_SEPARATOR.'Framework'.DIRECTORY_SEPARATOR.'ApiPlatform'.DIRECTORY_SEPARATOR.'State'.DIRECTORY_SEPARATOR.'Provider',
        '%s'.DIRECTORY_SEPARATOR.'Presentation',
        '%s'.DIRECTORY_SEPARATOR.'Presentation'.DIRECTORY_SEPARATOR.'Api',
        '%s'.DIRECTORY_SEPARATOR.'Presentation'.DIRECTORY_SEPARATOR.'Api'.DIRECTORY_SEPARATOR.'Resource',
        '%s'.DIRECTORY_SEPARATOR.'Presentation'.DIRECTORY_SEPARATOR.'Api'.DIRECTORY_SEPARATOR.'Mapper',
        '%s'.DIRECTORY_SEPARATOR.'Presentation'.DIRECTORY_SEPARATOR.'Web',
        '%s'.DIRECTORY_SEPARATOR.'Presentation'.DIRECTORY_SEPARATOR.'Web'.DIRECTORY_SEPARATOR.'Controller',
        '%s'.DIRECTORY_SEPARATOR.'Presentation'.DIRECTORY_SEPARATOR.'Cli',
        '%s'.DIRECTORY_SEPARATOR.'Presentation'.DIRECTORY_SEPARATOR.'Cli'.DIRECTORY_SEPARATOR.'Command',
    ];
    private const string TEMPLATE_DIR = __DIR__.'/../Template';

    public function __construct(
        private readonly ParameterBagInterface $parameters,
        private readonly DomainService $domainService,
        private readonly AskService $askService,
        private ?Generator $generator = null,
        private ?ConsoleStyle $io = null,
        private ?string $rootPath = null,
    ) {
        $this->rootPath = $this->parameters->get('kernel.project_dir').'/src/';
    }

    public function setGenerator(Generator $generator): void
    {
        $this->generator = $generator;
    }

    public function setConsoleStyle(ConsoleStyle $io): void
    {
        $this->io = $io;
        $this->askService->setConsoleStyle($io);
    }

    /**
     * @throws Exception
     */
    public function makeModel(?string $domainName = null, ?string $modelName = null): array
    {
        $isAggregateRoot = false;
        $isTimestampable = false;
        $isBlameable = false;
        $isApiResource = false;
        $fieldsForApiResource = [];

        if (empty($domainName)) {
            $domainName = $this->askService->domainNameQuestion(true, true);
        }

        if (false === $this->domainService->checkDomainExist($domainName)) {
            throw new RuntimeCommandException('You can\'t create a model on a non-existent domain.');
        }

        if (empty($modelName)) {
            $modelName = $this->askService->modelNameQuestion($domainName, false);
        }

        // Prepare class details & existence before asking AR/Timestampable/Blameable
        $modelClassNameDetails = $this->generator->createClassNameDetails(
            $modelName,
            $domainName.'\\Domain\\Model'
        );
        $classExists = class_exists($modelClassNameDetails->getFullName()) || $this->domainService->checkModelExist($domainName, $modelName);
        $modelPath = null;

        // Ask questions ONLY if the model does NOT already exist
        if (!$classExists) {
            if (class_exists(AggregateRoot::class)) {
                $isAggregateRoot = $this->askService->isAggregateRootQuestion();
            }

            if (interface_exists(TimestampableInterface::class)) {
                $isTimestampable = $this->askService->isTimestampableQuestion();
            }

            if (interface_exists(BlameableInterface::class)) {
                $isBlameable = $this->askService->isBlameableQuestion();
            }

            if (class_exists(ApiResource::class)) {
                $isApiResource = $this->askService->isApiResourceQuestion($modelName);
            }
        }

        if (!$classExists) {
            $this->generator->generateClass(
                $modelClassNameDetails->getFullName(),
                self::TEMPLATE_DIR . '/Model.tpl.php',
                [
                    // Let Maker's Generator inject class_name & namespace automatically
                    'use_statements' => '',
                    'is_aggregate_root' => $isAggregateRoot,
                    'is_timestampable' => $isTimestampable,
                    'is_blameable' => $isBlameable,
                ]
            );
        }

        if ($classExists) {
            $modelPath = self::getPathOfClass($modelClassNameDetails->getFullName());
            $this->io->text(['Your model already exists! So let\'s add some new fields!',]);
        } else {
            $this->io->text([
                '',
                'Model generated! Now let\'s add some fields!',
                'You can always add more fields later manually or by re-running this command.',
            ]);
        }

        // ensure the model file exists on disk before manipulating it
        if (!$classExists) {
            $this->generator->writeChanges();
            // Compute the path directly for a fresh class to avoid Reflection/autoload issues
            $modelPath = $this->rootPath . $domainName . '/Domain/Model/' . $modelName . '.php';
        } elseif (null === $modelPath) {
            $modelPath = self::getPathOfClass($modelClassNameDetails->getFullName());
        }

        // Update existing model: extends/implements/traits and add fields like make:entity
        if ($modelPath && is_file($modelPath)) {
            $code = file_get_contents($modelPath) ?: '';

            // If AggregateRoot selected and class has no extends yet, add it
            if ($isAggregateRoot && !preg_match('/class\s+' . preg_quote($modelName, '/') . '\s+extends\s+/s', $code)) {
                $code = preg_replace(
                    '/class\s+' . preg_quote($modelName, '/') . '(\s+)/',
                    'class ' . $modelName . ' extends AggregateRoot$1',
                    $code,
                    1
                );
            }

            // Prepare manipulator on current code
            $manipulator = new ClassSourceManipulator(
                sourceCode: $code,
                overwrite: false,
                useAttributesForDoctrineMapping: true,
            );
            $manipulator->setIo($this->io);

            // Add necessary use statements / interfaces / traits
            // Note: For new classes, Model.tpl.php already adds AggregateRoot/Timestampable/Blameable
            // implements/traits when selected. To avoid duplicates, we do NOT add them again here.
            // For existing classes, per requirements, we do not alter these either.
            // Therefore, skip manipulator-based interface/trait additions entirely.

            // Interactive fields addition (basic scalar fields with Doctrine attributes)
            $currentFields = $this->getPropertyNames($modelClassNameDetails->getFullName());

            $isFirstField = true;
            while (true) {
                $fieldName = $this->askService->addFieldQuestion($isFirstField, $currentFields);

                $isFirstField = false;
                if (!$fieldName) {
                    break;
                }

                // Guess default type
                $defaultType = 'string';
                $snake = Str::asSnakeCase($fieldName);
                $suffix = substr($snake, -3);
                if ('_at' === $suffix) {
                    $defaultType = 'datetime_immutable';
                } elseif ('_id' === $suffix) {
                    $defaultType = 'integer';
                } elseif (str_starts_with($snake, 'is_') || str_starts_with($snake, 'has_')) {
                    $defaultType = 'boolean';
                }

                // Build list of known dbal types
                $typesMap = Type::getTypesMap();
                $allValidTypes = array_keys($typesMap);

                // Extend supported types to include relations
                $relationTypes = [
                    EntityRelation::MANY_TO_ONE,
                    EntityRelation::ONE_TO_ONE,
                    EntityRelation::MANY_TO_MANY,
                    EntityRelation::ONE_TO_MANY,
                    'relation',
                ];

                $type = null;
                while (null === $type) {
                    $type = $this->askService->fieldTypeQuestion($defaultType, $allValidTypes, $relationTypes);
                    if ('?' === $type) {
                        $this->io->text('Main types: string, text, boolean, integer, float, json, datetime_immutable, date_immutable');
                        $this->io->text('Relations: relation, many_to_one, one_to_one, many_to_many, one_to_many');
                        $type = null;
                    } elseif (!in_array($type, array_merge($allValidTypes, $relationTypes), true)) {
                        $this->io->error(sprintf('Invalid type "%s".', $type));
                        $type = null;
                    }
                }

                $currentFqcn = $modelClassNameDetails->getFullName();

                // Handle Doctrine relations
                if (in_array($type, $relationTypes, true)) {
                    // Determine final relation type
                    if ('relation' === $type) {
                        // Display help table like make:entity
                        $this->io->writeln('What type of relationship is this?');
                        $rows = [];
                        $rows[] = [EntityRelation::MANY_TO_ONE, sprintf("Each %s relates to one %s. Each %s can relate to many %s.", Str::getShortClassName($currentFqcn), Str::getShortClassName($targetAnswer ?? 'Target'), Str::getShortClassName($targetAnswer ?? 'Target'), Str::getShortClassName($currentFqcn))];
                        $rows[] = ['', ''];
                        $rows[] = [EntityRelation::ONE_TO_MANY, sprintf("Each %s can relate to many %s. Each %s relates to one %s.", Str::getShortClassName($currentFqcn), Str::getShortClassName($targetAnswer ?? 'Target'), Str::getShortClassName($targetAnswer ?? 'Target'), Str::getShortClassName($currentFqcn))];
                        $rows[] = ['', ''];
                        $rows[] = [EntityRelation::MANY_TO_MANY, sprintf("Each %s can relate to many %s. Each %s can also relate to many %s.", Str::getShortClassName($currentFqcn), Str::getShortClassName($targetAnswer ?? 'Target'), Str::getShortClassName($targetAnswer ?? 'Target'), Str::getShortClassName($currentFqcn))];
                        $rows[] = ['', ''];
                        $rows[] = [EntityRelation::ONE_TO_ONE, sprintf("Each %s relates to exactly one %s. Each %s also relates to exactly one %s.", Str::getShortClassName($currentFqcn), Str::getShortClassName($targetAnswer ?? 'Target'), Str::getShortClassName($targetAnswer ?? 'Target'), Str::getShortClassName($currentFqcn))];
                        $this->io->table(['Type', 'Description'], $rows);

                        $type = $this->askService->relationTypeQuestion();
                        if (!in_array($type, EntityRelation::getValidRelationTypes(), true)) {
                            $this->io->error('Invalid relation type.');
                            continue;
                        }
                    }

                    // Ask for target class with autocompletion, like make:entity
                    $targetAnswer = null;
                    $currentFqcn = $modelClassNameDetails->getFullName();
                    $allFqcns = $this->domainService->getAllModelFQCNs();
                    $shortNames = array_map(static fn ($fqcn) => Str::getShortClassName($fqcn), $allFqcns);

                    while (null === $targetAnswer) {
                        $candidate = $this->askService->relationModelQuestion($allFqcns, $shortNames);
                        if (!$candidate) {
                            $this->io->error('Please provide a class name');
                            continue;
                        }
                        $resolved = $this->domainService->resolveModelFqcn(Str::asClassName($candidate), $domainName);
                        if (!$resolved) {
                            $this->io->error(sprintf('Unknown or ambiguous class "%s". Use a fully-qualified class name or a unique short name.', $candidate));
                            continue;
                        }
                        $targetAnswer = $resolved;
                    }

                    // Build relation object and ask inverse-side questions like make:entity
                    $shortCurrent = Str::getShortClassName($currentFqcn);
                    $shortTarget = Str::getShortClassName($targetAnswer);

                    // Helper closures for questions
                    $askFieldName = fn (string $targetClass, string $defaultValue) => $this->io->ask(
                        sprintf('New field name inside %s', Str::getShortClassName($targetClass)),
                        $defaultValue,
                        function ($name) {
                            // allow duplicates within same run? We'll keep simple non-empty
                            if (!$name) {
                                throw new \InvalidArgumentException('Field name cannot be empty.');
                            }
                            return $name;
                        }
                    );
                    $askIsNullable = fn (string $propertyName, string $targetClass) => $this->io->confirm(
                        sprintf('Is the %s.%s property allowed to be null (nullable)?', Str::getShortClassName($targetClass), $propertyName),
                        false
                    );

                    // initialize relation according to selected type
                    switch ($type) {
                        case EntityRelation::MANY_TO_ONE:
                            $relation = new EntityRelation(
                                EntityRelation::MANY_TO_ONE,
                                $currentFqcn,
                                $targetAnswer
                            );
                            $relation->setOwningProperty($fieldName);
                            $relation->setIsNullable($askIsNullable($relation->getOwningProperty(), $relation->getOwningClass()));

                            // Ask to map inverse side (recommended)
                            $mapInverse = $this->io->confirm(
                                sprintf(
                                    'Do you want to add a new property to %s so that you can access/update %s objects from it?',
                                    Str::getShortClassName($relation->getInverseClass()),
                                    Str::getShortClassName($relation->getOwningClass())
                                ),
                                true
                            );
                            $relation->setMapInverseRelation($mapInverse);
                            if ($relation->getMapInverseRelation()) {
                                $relation->setInverseProperty($askFieldName(
                                    $relation->getInverseClass(),
                                    Str::singularCamelCaseToPluralCamelCase(Str::getShortClassName($relation->getOwningClass()))
                                ));
                                if (!$relation->isNullable()) {
                                    // orphanRemoval only if not nullable
                                    $this->io->text('OrphanRemoval will remove related objects when they are removed from the collection.');
                                    $relation->setOrphanRemoval($this->io->confirm('Enable orphanRemoval?', false));
                                }
                            }
                            break;

                        case EntityRelation::ONE_TO_MANY:
                            // Create a ManyToOne on the target and OneToMany here
                            $relation = new EntityRelation(
                                EntityRelation::MANY_TO_ONE,
                                $targetAnswer,
                                $currentFqcn
                            );
                            $relation->setInverseProperty($fieldName);
                            $this->io->comment(sprintf(
                                'A new property will be added to %s so you can access/set the related %s object from it.',
                                Str::getShortClassName($relation->getOwningClass()),
                                Str::getShortClassName($relation->getInverseClass())
                            ));
                            $relation->setOwningProperty($askFieldName(
                                $relation->getOwningClass(),
                                Str::asLowerCamelCase(Str::getShortClassName($relation->getInverseClass()))
                            ));
                            $relation->setIsNullable($askIsNullable($relation->getOwningProperty(), $relation->getOwningClass()));
                            if (!$relation->isNullable()) {
                                $this->io->text('OrphanRemoval deletes the owning object when removed from the collection.');
                                $relation->setOrphanRemoval($this->io->confirm('Enable orphanRemoval?', false));
                            }
                            break;

                        case EntityRelation::MANY_TO_MANY:
                            $relation = new EntityRelation(
                                EntityRelation::MANY_TO_MANY,
                                $currentFqcn,
                                $targetAnswer
                            );
                            $relation->setOwningProperty($fieldName);
                            $mapInverse = $this->io->confirm(
                                sprintf(
                                    'Do you want to add a new property to %s so that you can access the related %s objects from it?',
                                    Str::getShortClassName($relation->getInverseClass()),
                                    Str::getShortClassName($relation->getOwningClass())
                                ),
                                true
                            );
                            $relation->setMapInverseRelation($mapInverse);
                            if ($relation->getMapInverseRelation()) {
                                $relation->setInverseProperty($askFieldName(
                                    $relation->getInverseClass(),
                                    Str::singularCamelCaseToPluralCamelCase(Str::getShortClassName($relation->getOwningClass()))
                                ));
                            }
                            break;

                        case EntityRelation::ONE_TO_ONE:
                            $relation = new EntityRelation(
                                EntityRelation::ONE_TO_ONE,
                                $currentFqcn,
                                $targetAnswer
                            );
                            $relation->setOwningProperty($fieldName);
                            $relation->setIsNullable($askIsNullable($relation->getOwningProperty(), $relation->getOwningClass()));
                            $mapInverse = $this->io->confirm(
                                sprintf(
                                    'Do you want to add a new property to %s so that you can access the related %s object from it?',
                                    Str::getShortClassName($relation->getInverseClass()),
                                    Str::getShortClassName($relation->getOwningClass())
                                ),
                                false
                            );
                            $relation->setMapInverseRelation($mapInverse);
                            if ($relation->getMapInverseRelation()) {
                                $relation->setInverseProperty($askFieldName(
                                    $relation->getInverseClass(),
                                    Str::asLowerCamelCase(Str::getShortClassName($relation->getOwningClass()))
                                ));
                            }
                            break;
                        default:
                            $this->io->error('Unsupported relation type.');
                            continue 2;
                    }

                    // Apply on this class and possibly the other class
                    $fileOperations = [];
                    $fileOperations[$modelPath] = $manipulator;

                    if ($type === EntityRelation::MANY_TO_ONE) {
                        $manipulator->addManyToOneRelation($relation->getOwningRelation());
                        if ($relation->getMapInverseRelation()) {
                            $otherPath = self::getPathOfClass($relation->getInverseClass());
                            $otherManipulator = new \Symfony\Bundle\MakerBundle\Util\ClassSourceManipulator(file_get_contents($otherPath) ?: '', false, true);
                            $otherManipulator->setIo($this->io);
                            $otherManipulator->addOneToManyRelation($relation->getInverseRelation());
                            $fileOperations[$otherPath] = $otherManipulator;
                        }
                    } elseif ($type === EntityRelation::MANY_TO_MANY) {
                        $manipulator->addManyToManyRelation($relation->getOwningRelation());
                        if ($relation->getMapInverseRelation()) {
                            $otherPath = self::getPathOfClass($relation->getInverseClass());
                            $otherManipulator = new \Symfony\Bundle\MakerBundle\Util\ClassSourceManipulator(file_get_contents($otherPath) ?: '', false, true);
                            $otherManipulator->setIo($this->io);
                            $otherManipulator->addManyToManyRelation($relation->getInverseRelation());
                            $fileOperations[$otherPath] = $otherManipulator;
                        }
                    } elseif ($type === EntityRelation::ONE_TO_ONE) {
                        $manipulator->addOneToOneRelation($relation->getOwningRelation());
                        if ($relation->getMapInverseRelation()) {
                            $otherPath = self::getPathOfClass($relation->getInverseClass());
                            $otherManipulator = new \Symfony\Bundle\MakerBundle\Util\ClassSourceManipulator(file_get_contents($otherPath) ?: '', false, true);
                            $otherManipulator->setIo($this->io);
                            $otherManipulator->addOneToOneRelation($relation->getInverseRelation());
                            $fileOperations[$otherPath] = $otherManipulator;
                        }
                    } elseif ($type === EntityRelation::ONE_TO_MANY) {
                        // Save inverse side (this class) and owning side on target
                        $otherPath = self::getPathOfClass($relation->getOwningClass());
                        $otherManipulator = new \Symfony\Bundle\MakerBundle\Util\ClassSourceManipulator(file_get_contents($otherPath) ?: '', false, true);
                        $otherManipulator->setIo($this->io);

                        $otherManipulator->addManyToOneRelation($relation->getOwningRelation());
                        $manipulator->addOneToManyRelation($relation->getInverseRelation());

                        $fileOperations[$otherPath] = $otherManipulator;
                    }

                    foreach ($fileOperations as $path => $manip) {
                        file_put_contents($path, $manip->getSourceCode());
                    }

                    $currentFields[] = $fieldName;
                    continue; // next property
                }

                // Normal scalar field flow
                $classProperty = new \Symfony\Bundle\MakerBundle\Util\ClassSource\Model\ClassProperty(
                    propertyName: $fieldName,
                    type: $type
                );

                if ('string' === $type) {
                    $classProperty->length = $this->io->ask('Field length', '255', \Symfony\Bundle\MakerBundle\Validator::validateLength(...));
                } elseif ('decimal' === $type) {
                    $classProperty->precision = $this->io->ask('Precision (total number of digits stored)', '10', \Symfony\Bundle\MakerBundle\Validator::validatePrecision(...));
                    $classProperty->scale = $this->io->ask('Scale (number of decimals to store)', '0', \Symfony\Bundle\MakerBundle\Validator::validateScale(...));
                }

                if ($this->io->confirm('Can this field be null in the database (nullable)?', false)) {
                    $classProperty->nullable = true;
                }

                $manipulator->addEntityField($classProperty);
                $currentFields[] = $fieldName;

                // collect for ApiResource DTO (ignore relations)
                $fieldsForApiResource[] = [
                    'name' => $fieldName,
                    'type' => $type,
                    'nullable' => (bool) ($classProperty->nullable ?? false),
                ];

                // Persist file after each field addition for safety
                file_put_contents($modelPath, $manipulator->getSourceCode());
            }

            // Final write in case no fields were added but interfaces/traits/extends changed
            file_put_contents($modelPath, $manipulator->getSourceCode());
        }

        $this->makeRepositoryInterface($domainName, $modelName);
        $this->makeDoctrineRepository($domainName, $modelName);

        if ($isApiResource) {
            $this->makeApiResource(
                $domainName,
                $modelName,
                $isAggregateRoot,
                $isTimestampable,
                $isBlameable,
                $fieldsForApiResource
            );
        }

        $this->generator->writeChanges();

        return [
            'domainName' => $domainName,
            'modelName' => $modelName,
        ];
    }

    public function makeDomain(?string $domainName = null, bool $askQuestion = true): string
    {
        if (true === $askQuestion && empty($domainName)) {
            $domainName = $this->askService->domainNameQuestion(false, false);
        }

        if (true === $this->domainService->checkDomainExist($domainName)) {
            throw new RuntimeCommandException(sprintf('Domain "%s" already exists.', $domainName));
        }

        $domainPath = $this->rootPath . $domainName;

        mkdir($domainPath);
        $this->io->success(sprintf('Folder "%s" created.', $domainName));

        foreach (self::DOMAIN_FOLDERS as $folder) {
            $path = sprintf($folder, $domainPath);
            mkdir($path);
            $this->io->success(sprintf('Folder "%s" created.', $path));
        }

        $fileConfigName = u($domainName)->snake()->toString() . '.php';

        $this->generator->generateFile(
            $this->rootPath . '../config/services/'.$fileConfigName,
            self::TEMPLATE_DIR . '/Config.tpl.php',
            [
                'domainName' => $domainName,
            ]
        );

        $this->generator->writeChanges();

        return $domainPath;
    }

    /**
     * @throws Exception
     */
    public function makeRepositoryInterface(
        string $domainName,
        string $modelName,
        bool $generate = true,
    ): void
    {
        $commandClassNameDetails = $this->generator->createClassNameDetails(
            $modelName.'RepositoryInterface',
            $domainName.'\\Domain\\Repository'
        );

        $fqcn = $commandClassNameDetails->getFullName();
        if (class_exists($fqcn) || interface_exists($fqcn)) {
            if (null !== $this->io) {
                $this->io->text(sprintf('Repository interface "%s" already exists, skipping.', $fqcn));
            }
            return;
        }

        $this->generator->generateClass(
            $fqcn,
            self::TEMPLATE_DIR . '/RepositoryInterface.tpl.php',
            [
                'use_statements' => 'use App\\'.$domainName.'\\Domain\\Model\\'.$modelName.';',
                'model_class_name' => $modelName,
                'var_model_class_name' => Str::asLowerCamelCase($modelName),
                'var_model_class_alias' => Str::asSnakeCase($modelName),
            ]
        );

        if ($generate) {
            $this->generator->writeChanges();
        }
    }

    /**
     * @throws Exception
     */
    public function makeDoctrineRepository(
        string $domainName,
        string $modelName,
        bool $generate = true,
    ): void
    {
        $commandClassNameDetails = $this->generator->createClassNameDetails(
            'Doctrine'.$modelName.'Repository',
            $domainName.'\\Infrastructure\\Doctrine\\Repository'
        );

        $fqcn = $commandClassNameDetails->getFullName();
        if (class_exists($fqcn) || interface_exists($fqcn)) {
            if (null !== $this->io) {
                $this->io->text(sprintf('Doctrine repository "%s" already exists, skipping.', $fqcn));
            }
            return;
        }

        $this->generator->generateClass(
            $fqcn,
            self::TEMPLATE_DIR . '/Repository.tpl.php',
            [
                'domain' => $domainName,
                'model_class_name' => $modelName,
                'var_model_class_name' => Str::asLowerCamelCase($modelName),
                'var_model_class_alias' => Str::asSnakeCase($modelName),
            ]
        );

        if ($generate) {
            $this->generator->writeChanges();
        }
    }

    /**
     * @throws Exception
     */
    public function makeApiResource(
        ?string $domainName = null,
        ?string $modelName = null,
        bool $isAggregateRoot = false,
        bool $isTimestampable = true,
        bool $isBlameable = true,
        array $fields = [],
        bool $generate = true,
    ): void
    {
        if (empty($domainName)) {
            $domainName = $this->askService->domainNameQuestion(true, true);
        }

        if (false === $this->domainService->checkDomainExist($domainName)) {
            throw new RuntimeCommandException('You can\'t create a model on a non-existent domain.');
        }

        if (empty($modelName)) {
            $modelName = $this->askService->modelNameQuestion($domainName, false);
        }

        if (false === $this->domainService->checkModelExist($domainName, $modelName)) {
            throw new RuntimeCommandException('You can\'t create a model on a non-existent domain.');
        }

        // If no fields were provided, introspect the model class and collect non-system properties
        if (empty($fields)) {
            $fqcn = sprintf('App\\%s\\Domain\\Model\\%s', $domainName, $modelName);
            if (class_exists($fqcn)) {
                $exclude = ['id', 'createdAt', 'updatedAt', 'createdBy', 'updatedBy'];
                $refl = new \ReflectionClass($fqcn);
                foreach ($refl->getProperties() as $prop) {
                    if ($prop->isStatic()) {
                        continue;
                    }
                    $name = $prop->getName();
                    if (in_array($name, $exclude, true)) {
                        continue;
                    }
                    $nullable = true;
                    $dbalType = 'mixed';
                    $type = $prop->getType();
                    if ($type instanceof \ReflectionNamedType) {
                        $nullable = $type->allowsNull();
                        $phpType = ltrim($type->getName(), '\\');
                        $dbalType = match ($phpType) {
                            'string' => 'string',
                            'int', 'integer' => 'integer',
                            'bool', 'boolean' => 'boolean',
                            'float', 'double' => 'float',
                            'array' => 'json',
                            'DateTimeInterface', 'DateTimeImmutable', 'DateTime' => 'datetime_immutable',
                            default => 'mixed',
                        };
                    }
                    $fields[] = [
                        'name' => $name,
                        'type' => $dbalType,
                        'nullable' => (bool) $nullable,
                    ];
                }
            }
        }

        $commandClassNameDetails = $this->generator->createClassNameDetails(
            $modelName . 'Resource',
            $domainName . '\\Infrastructure\\ApiPlatform\\Resource'
        );

        $this->generator->generateClass(
            $commandClassNameDetails->getFullName(),
            self::TEMPLATE_DIR . '/ApiResource.tpl.php',
            [
                'domain' => $domainName,
                'model_class_name' => $modelName,
                'var_short_name' => Str::asSnakeCase($modelName),
                'is_aggregate_root' => $isAggregateRoot,
                'is_timestampable' => $isTimestampable,
                'is_blameable' => $isBlameable,
                'fields' => $fields,
            ]
        );

        if ($generate) {
            $this->generator->writeChanges();
        }
    }

    private function getPathOfClass(string $class): string
    {
        return (new ClassDetails($class))->getPath();
    }

    /**
     * @return string[]
     */
    private function getPropertyNames(string $class): array
    {
        if (!class_exists($class)) {
            return [];
        }

        $reflClass = new \ReflectionClass($class);

        return array_map(static fn (\ReflectionProperty $prop) => $prop->getName(), $reflClass->getProperties());
    }
}
