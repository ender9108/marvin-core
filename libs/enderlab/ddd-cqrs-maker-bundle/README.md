# DDD-CQRS-MAKER-BUNDLE - Technical Documentation

## Vue d'ensemble

Le **ddd-cqrs-maker-bundle** est une collection de générateurs de code (Maker commands) pour scaffolder rapidement des éléments d'une architecture DDD/CQRS dans un projet Symfony. Il étend le système de makers de Symfony pour générer automatiquement des bounded contexts, commands, queries, models, value objects, fixtures, et commandes CLI.

## Architecture

### Structure du bundle

```
src/
├── DddCqrsMakerBundle.php
└── Maker/
    ├── MakeBoundedContextCommand.php       # Génère l'arborescence d'un bounded context
    ├── MakeApplicationCommandCommand.php   # Génère Command + CommandHandler
    ├── MakeQueryCommand.php                # Génère Query + QueryHandler
    ├── MakeModelCommand.php                # Génère Model + Repository + Mapping Doctrine
    ├── MakeValueObjectCommand.php          # Génère ValueObject (enum ou class)
    ├── MakeEventHandlerCommand.php         # Génère EventHandler pour événements de domaine
    ├── MakeFixtureCommand.php              # Génère Fixture + Factory (Foundry)
    └── MakeCliCommandCommand.php           # Génère commande Symfony CLI
```

---

## Makers Disponibles

### 1. make:bounded-context

**Fichier:** `src/Maker/MakeBoundedContextCommand.php`

Crée l'arborescence complète d'un bounded context DDD.

**Signature:**
```bash
php bin/console make:bounded-context [name]
```

**Usage:**
```bash
php bin/console make:bounded-context Device
```

**Structure générée:**
```
src/Device/
├── Application/
│   ├── Command/
│   ├── CommandHandler/
│   ├── Query/
│   ├── QueryHandler/
│   └── EventHandler/
├── Domain/
│   ├── Model/
│   ├── ValueObject/
│   │   └── Identity/
│   ├── Repository/
│   ├── Event/
│   ├── Exception/
│   ├── List/
│   └── Service/
├── Infrastructure/
│   ├── Framework/Symfony/
│   │   ├── Service/
│   │   ├── EventListener/
│   │   ├── Validator/
│   │   ├── MapperTransformer/
│   │   └── DataFixtures/
│   ├── Persistence/Doctrine/
│   │   ├── DBAL/Types/
│   │   ├── ORM/
│   │   └── EventListener/
│   └── Messaging/
│       ├── Producer/
│       └── Consumer/
├── Presentation/
│   ├── Cli/Command/
│   └── Api/
│       ├── Resource/
│       ├── State/Processor/
│       └── State/Provider/
└── README.md

config/doctrine/Device/
└── .gitkeep
```

**Fonctionnalités:**
- Crée tous les répertoires nécessaires pour un bounded context
- Génère un README.md avec instructions
- Crée le répertoire de configuration Doctrine
- Ajoute des fichiers `.gitkeep` pour conserver les dossiers vides

**Options interactives:**
- Nom du bounded context
- Confirmation avant génération

---

### 2. make:application-command

**Fichier:** `src/Maker/MakeApplicationCommandCommand.php`

Génère une Command et son CommandHandler.

**Signature:**
```bash
php bin/console make:application-command [context] [name]
```

**Usage:**
```bash
php bin/console make:application-command Device CreateDevice
```

**Questions interactives:**
1. **Bounded context:** Choix parmi les contexts existants ou nouveau
2. **Nom de la Command:** Ex: `CreateDevice`, `UpdateDeviceName`
3. **Synchrone ?** Oui (SyncCommandInterface) ou Non (CommandInterface)
4. **Sous-dossier:** Optionnel (ex: `Device`, `Zone`)
5. **Paramètres de la Command:**
   - Nom du paramètre
   - Type du paramètre
   - Répéter jusqu'à "stop"
6. **Dépendances du Handler:**
   - Nom de la dépendance
   - Type/Classe de la dépendance
   - Nom de la propriété
   - Répéter jusqu'à "stop"

**Fichiers générés:**

**Command (Async):**
```php
// src/Device/Application/Command/CreateDevice.php
namespace Marvin\Device\Application\Command;

use EnderLab\DddCqrs\Application\Command\CommandInterface;

final readonly class CreateDevice implements CommandInterface
{
    public function __construct(
        public DeviceName $name,
        public DeviceType $type,
    ) {}
}
```

**Command (Sync):**
```php
// src/Device/Application/Command/UpdateDeviceName.php
namespace Marvin\Device\Application\Command;

use EnderLab\DddCqrs\Application\Command\SyncCommandInterface;

final readonly class UpdateDeviceName implements SyncCommandInterface
{
    public function __construct(
        public DeviceId $deviceId,
        public DeviceName $name,
    ) {}
}
```

**CommandHandler:**
```php
// src/Device/Application/CommandHandler/CreateDeviceHandler.php
namespace Marvin\Device\Application\CommandHandler;

use Marvin\Device\Application\Command\CreateDevice;
use EnderLab\DddCqrs\Application\Command\CommandHandlerInterface;
use Psr\Log\LoggerInterface;

final readonly class CreateDeviceHandler implements CommandHandlerInterface
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository,
    ) {}

    public function __invoke(CreateDevice $command): void
    {
        // TODO: Implémenter la logique de CreateDevice

        // Exemple avec Repository :
        // $entity = $this->deviceRepository->byId($command->entityId);
        // $entity->doSomething();
        // $this->deviceRepository->save($entity);
    }
}
```

**Notes générées automatiquement:**
- Command SYNCHRONE ou ASYNCHRONE selon le choix
- Le Handler sera automatiquement découvert par Symfony Messenger
- Configuration du routing Messenger si besoin

---

### 3. make:query

**Fichier:** `src/Maker/MakeQueryCommand.php`

Génère une Query et son QueryHandler.

**Signature:**
```bash
php bin/console make:query [context] [name]
```

**Usage:**
```bash
php bin/console make:query Device GetDeviceById
```

**Questions interactives:**
1. **Bounded context**
2. **Nom de la Query:** Ex: `GetDeviceById`, `ListDevices`
3. **Sous-dossier:** Optionnel
4. **Type de retour du QueryHandler:** Ex: `?Device`, `array`, `DeviceCollection`
5. **Paramètres de la Query:**
   - Nom du paramètre
   - Type du paramètre
   - Nullable ?
   - Valeur par défaut ?
6. **Dépendances du QueryHandler:**
   - Repositories, QueryBus, Logger, etc.

**Fichiers générés:**

**Query:**
```php
// src/Device/Application/Query/GetDeviceById.php
namespace Marvin\Device\Application\Query;

use Enderlab\DddCqrs\Application\Query\QueryInterface;

final readonly class GetDeviceById implements QueryInterface
{
    public function __construct(
        public DeviceId $deviceId,
    ) {}
}
```

**QueryHandler:**
```php
// src/Device/Application/QueryHandler/GetDeviceByIdHandler.php
namespace Marvin\Device\Application\QueryHandler;

use Marvin\Device\Application\Query\GetDeviceById;
use Enderlab\DddCqrs\Application\Query\QueryHandlerInterface;
use Psr\Log\LoggerInterface;

final readonly class GetDeviceByIdHandler implements QueryHandlerInterface
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository,
    ) {}

    public function __invoke(GetDeviceById $query): ?Device
    {
        // TODO: Implémenter la logique de GetDeviceById

        // Pattern Get détecté - Exemple :
        // $entity = $this->deviceRepository->byId($query->id);
        //
        // if (!$entity) {
        //     throw new \RuntimeException('Entity not found');
        // }
        //
        // return $entity; // ou un DTO

        return null;
    }
}
```

**Patterns détectés automatiquement:**
- **Get** (ex: `GetDeviceById`) → Génère exemple avec `byId()`
- **List** (ex: `ListDevices`) → Génère exemple avec `findAll()` et pagination
- **Search** → Génère exemple de recherche

**Notes:**
- Les Queries sont **TOUJOURS SYNCHRONES**
- Type de retour personnalisable
- Le QueryHandler sera automatiquement découvert

---

### 4. make:model

**Fichier:** `src/Maker/MakeModelCommand.php`

Génère un Model de domaine complet avec Repository, Identity ValueObject, DBAL Type, et mapping Doctrine XML.

**Signature:**
```bash
php bin/console make:model [context] [model]
```

**Usage:**
```bash
php bin/console make:model Device Device
```

**Questions interactives:**
1. **Bounded context**
2. **Nom du model:** Ex: `Device`, `Zone`, `User`
3. **Champs du model:**
   - Nom du champ
   - Type du champ:
     - Types standard: `string`, `int`, `datetime`, `json`, etc.
     - ValueObjects: `DeviceName`, `Email`, etc.
     - Relations: `oneToOne`, `oneToMany`, `manyToOne`, `manyToMany`
   - Nullable ?
   - Configuration relation si applicable
   - Répéter jusqu'à "stop"

**Fichiers générés:**

**1. Identity ValueObject:**
```php
// src/Device/Domain/ValueObject/Identity/DeviceId.php
namespace Marvin\Device\Domain\ValueObject\Identity;

use Symfony\Component\Uid\UuidV7;

final class DeviceId extends UuidV7
{
}
```

**2. Model:**
```php
// src/Device/Domain/Model/Device.php
namespace Marvin\Device\Domain\Model;

use DateTimeImmutable;
use DateTimeInterface;
use Marvin\Device\Domain\ValueObject\Identity\DeviceId;

final class Device
{
    public readonly DeviceId $id;

    public function __construct(
        private(set) DeviceName $name,
        private(set) DeviceType $type,
        private(set) DateTimeInterface $createdAt = new DateTimeImmutable(),
    ) {
        $this->id = new DeviceId();
    }
}
```

**3. Repository Interface:**
```php
// src/Device/Domain/Repository/DeviceRepositoryInterface.php
namespace Marvin\Device\Domain\Repository;

use Marvin\Device\Domain\Model\Device;
use Marvin\Device\Domain\ValueObject\Identity\DeviceId;

interface DeviceRepositoryInterface
{
    public function save(Device $device): void;
    
    public function byId(DeviceId $id): ?Device;
    
    public function findAll(): array;
}
```

**4. ORM Repository:**
```php
// src/Device/Infrastructure/Persistence/Doctrine/ORM/DeviceOrmRepository.php
namespace Marvin\Device\Infrastructure\Persistence\Doctrine\ORM;

use Doctrine\ORM\EntityManagerInterface;
use Marvin\Device\Domain\Model\Device;
use Marvin\Device\Domain\Repository\DeviceRepositoryInterface;
use Marvin\Device\Domain\ValueObject\Identity\DeviceId;

final readonly class DeviceOrmRepository implements DeviceRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function save(Device $device): void
    {
        $this->entityManager->persist($device);
        $this->entityManager->flush();
    }

    public function byId(DeviceId $id): ?Device
    {
        return $this->entityManager->find(Device::class, $id);
    }

    public function findAll(): array
    {
        return $this->entityManager->getRepository(Device::class)->findAll();
    }
}
```

**5. DBAL Type:**
```php
// src/Device/Infrastructure/Persistence/Doctrine/DBAL/Types/DeviceIdType.php
namespace Marvin\Device\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\GuidType;
use Marvin\Device\Domain\ValueObject\Identity\DeviceId;

final class DeviceIdType extends GuidType
{
    public const NAME = 'device_id';

    public function getName(): string
    {
        return self::NAME;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?DeviceId
    {
        return $value ? DeviceId::fromString($value) : null;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        return $value instanceof DeviceId ? $value->toString() : $value;
    }
}
```

**6. Mapping Doctrine XML:**
```xml
<!-- config/doctrine/Device/Device.orm.xml -->
<?xml version="1.0" encoding="UTF-8"?>
<doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                  xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                  https://www.doctrine-project.org/schemas/orm/doctrine-mapping.xsd">

    <entity name="Marvin\Device\Domain\Model\Device" table="device">
        <id name="id" type="device_id" column="id"/>
        
        <embedded name="name" class="Marvin\Device\Domain\ValueObject\DeviceName" use-column-prefix="false"/>
        
        <field name="type" type="string" column="type"/>
        <field name="createdAt" type="datetime_immutable" column="created_at"/>
    </entity>
</doctrine-mapping>
```

**Actions post-génération:**
1. Enregistrer le DBAL Type dans `config/packages/doctrine.yaml`
2. Créer les ValueObjects utilisés si besoin
3. Faire une migration: `php bin/console doctrine:migrations:diff`

---

### 5. make:value-object

**Fichier:** `src/Maker/MakeValueObjectCommand.php`

Génère un ValueObject (Enum ou Class).

**Signature:**
```bash
php bin/console make:value-object [context] [name] [type]
```

**Usage:**
```bash
php bin/console make:value-object Device DeviceStatus enum
```

**Questions interactives:**
1. **Bounded context:** Peut être `Shared` pour value objects partagés
2. **Nom du ValueObject:** Ex: `DeviceStatus`, `Email`, `Temperature`
3. **Type:** `enum` ou `class`
4. **Est-ce un ValueObject Identity ?** Oui/Non (génère une classe extends UuidV7)

**Fichiers générés:**

**Enum ValueObject:**
```php
// src/Device/Domain/ValueObject/DeviceStatus.php
namespace Marvin\Device\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectInterface;

enum DeviceStatus: string implements ValueObjectInterface
{
    case ONLINE = 'online';
    case OFFLINE = 'offline';
    case ERROR = 'error';

    public function equals(ValueObjectInterface $other): bool
    {
        return $other instanceof self && $this->value === $other->value;
    }

    public function toString(): string
    {
        return $this->value;
    }
}
```

**Class ValueObject:**
```php
// src/Device/Domain/ValueObject/DeviceName.php
namespace Marvin\Device\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectInterface;

final readonly class DeviceName implements ValueObjectInterface
{
    public string $value;

    public function __construct(string $name)
    {
        Assert::notEmpty($name);
        Assert::lengthBetween($name, 2, 100);

        $this->value = $name;
    }

    public function equals(DeviceName $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
```

**Identity ValueObject:**
```php
// src/Device/Domain/ValueObject/Identity/DeviceId.php
namespace Marvin\Device\Domain\ValueObject\Identity;

use Symfony\Component\Uid\UuidV7;

final class DeviceId extends UuidV7
{
}
```

---

### 6. make:event-handler

**Fichier:** `src/Maker/MakeEventHandlerCommand.php`

Génère un EventHandler pour réagir à un événement de domaine.

**Signature:**
```bash
php bin/console make:event-handler [context] [event]
```

**Usage:**
```bash
php bin/console make:event-handler Notification UserCreated
```

**Questions interactives:**
1. **Bounded context:** Context où sera le handler
2. **Nom de l'événement:** Ex: `UserCreated`, `DeviceStatusChanged`
3. **Context source de l'événement:** Context d'origine (peut être différent)
4. **Sous-dossier:** Optionnel
5. **Dépendances du Handler:**
   - EmailService, MessageBus, etc.

**Fichier généré:**
```php
// src/Notification/Application/EventHandler/UserCreatedHandler.php
namespace Marvin\Notification\Application\EventHandler;

use Marvin\Security\Domain\Event\User\UserCreated;
use EnderLab\DddCqrs\Application\Event\DomainEventHandlerInterface;

final readonly class UserCreatedHandler implements DomainEventHandlerInterface
{
    public function __construct(
        private EmailServiceInterface $emailService,
    ) {}

    public function __invoke(UserCreated $event): void
    {
        // TODO: Implémenter la logique de UserCreatedHandler
        
        // Envoyer email de bienvenue
        $this->emailService->send(
            to: $event->email,
            template: 'welcome'
        );
    }
}
```

**Pattern:**
- Communication inter-bounded-contexts
- Réaction asynchrone aux événements de domaine
- Découverte automatique via `#[AsMessageHandler]`

---

### 7. make:fixture

**Fichier:** `src/Maker/MakeFixtureCommand.php`

Génère une Fixture Doctrine et une Factory Foundry.

**Signature:**
```bash
php bin/console make:fixture [context] [model]
```

**Usage:**
```bash
php bin/console make:fixture Device Device
```

**Questions interactives:**
1. **Bounded context**
2. **Nom du model:** Ex: `Device`, `User`
3. **Créer des états nommés ?** (méthodes helper sur la factory)
   - Nom de l'état (ex: `zigbee`, `online`)
   - Champs à override avec valeurs par défaut
   - Répéter pour chaque état

**Fichiers générés:**

**Factory:**
```php
// tests/Factory/Device/DeviceFactory.php
namespace App\Tests\Factory\Device;

use Marvin\Device\Domain\Model\Device;
use Marvin\Device\Domain\ValueObject\Identity\DeviceId;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Device>
 */
final class DeviceFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Device::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'id' => DeviceId::generate(),
            'name' => self::faker()->unique()->slug(),
            'type' => self::faker()->randomElement(['light', 'sensor', 'switch']),
        ];
    }

    public function online(): self
    {
        return $this->with([
            'status' => 'online',
        ]);
    }

    public function zigbee(): self
    {
        return $this->with([
            'protocol' => 'zigbee',
            'type' => 'sensor',
        ]);
    }
}
```

**Fixture:**
```php
// src/Device/Infrastructure/Framework/Symfony/DataFixtures/DeviceFixtures.php
namespace Marvin\Device\Infrastructure\Framework\Symfony\DataFixtures;

use App\Tests\Factory\Device\DeviceFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DeviceFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Créer plusieurs Devices avec données aléatoires
        DeviceFactory::createMany(10);

        // Ou créer des Devices spécifiques
        // DeviceFactory::createOne([
        //     'name' => 'Example Device',
        // ]);

        $manager->flush();
    }
}
```

**Usage:**
```bash
# Charger les fixtures
php bin/console doctrine:fixtures:load

# Dans un test
DeviceFactory::createOne();
DeviceFactory::new()->zigbee()->online()->create();
```

---

### 8. make:cli-command

**Fichier:** `src/Maker/MakeCliCommandCommand.php`

Génère une commande Symfony CLI qui utilise Commands/Queries.

**Signature:**
```bash
php bin/console make:cli-command [context] [name]
```

**Usage:**
```bash
php bin/console make:cli-command Device CreateDevice
```

**Questions interactives:**
1. **Bounded context**
2. **Nom de la commande:** Ex: `CreateDevice`, `SyncDevices`
3. **Signature de la commande:** Ex: `marvin:device:create`
4. **Description de la commande**
5. **Type d'use case appelé:**
   - Command
   - Query
   - Les deux
   - Aucun (logique custom)
6. **Nom de la Command/Query à appeler**
7. **Arguments de la commande:**
   - Nom, description, required/optional
8. **Options de la commande:**
   - Nom, description, default value

**Fichier généré:**
```php
// src/Device/Presentation/Cli/Command/CreateDeviceCommand.php
namespace Marvin\Device\Presentation\Cli\Command;

use EnderLab\DddCqrs\Application\Command\SyncCommandBusInterface;
use Marvin\Device\Application\Command\CreateDevice;
use Marvin\Device\Domain\ValueObject\DeviceName;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:device:create',
    description: 'Crée un nouveau device',
)]
final class CreateDeviceCommand extends Command
{
    public function __construct(
        private readonly SyncCommandBusInterface $commandBus
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'Nom du device')
            ->addArgument('type', InputArgument::REQUIRED, 'Type du device');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $command = new CreateDevice(
                new DeviceName($input->getArgument('name')),
                $input->getArgument('type')
            );

            $this->commandBus->handle($command);

            $io->success('Device créé avec succès !');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }
    }
}
```

**Usage:**
```bash
php bin/console marvin:device:create "Lampe salon" light
```

**Pattern:**
- Les commandes CLI ne contiennent **aucune logique métier**
- Elles délèguent tout aux Command/Query Bus
- Présentation uniquement (input/output)

---

## Patterns et Conventions

### 1. Naming Conventions

**Commands:**
- Verbe à l'impératif: `CreateDevice`, `UpdateUserProfile`, `DeleteZone`
- Toujours suffixé par l'entité concernée

**Queries:**
- `Get` pour récupération unique: `GetDeviceById`, `GetUserByEmail`
- `List` pour collections: `ListDevices`, `ListUsers`
- `Find` pour recherche: `FindDevicesByZone`
- `Search` pour recherche complexe: `SearchDevicesWithFilters`

**Handlers:**
- Nom de la Command/Query + `Handler`: `CreateDeviceHandler`, `GetDeviceByIdHandler`

**Models:**
- Noms singuliers: `Device`, `User`, `Zone`
- PascalCase

**ValueObjects:**
- Noms descriptifs: `DeviceName`, `Email`, `Temperature`
- Suffixe selon le type: `DeviceId`, `UserId` (Identity)

**Repositories:**
- Interface: `DeviceRepositoryInterface`
- Implémentation ORM: `DeviceOrmRepository`

---

### 2. Workflow de Développement

**Créer un nouveau Bounded Context:**
```bash
# 1. Générer l'arborescence
php bin/console make:bounded-context Device

# 2. Créer le model principal
php bin/console make:model Device Device

# 3. Créer les value objects nécessaires
php bin/console make:value-object Device DeviceName class
php bin/console make:value-object Device DeviceStatus enum

# 4. Créer les commandes métier
php bin/console make:application-command Device CreateDevice
php bin/console make:application-command Device UpdateDeviceName

# 5. Créer les queries
php bin/console make:query Device GetDeviceById
php bin/console make:query Device ListDevices

# 6. Créer les fixtures pour les tests
php bin/console make:fixture Device Device

# 7. Créer une commande CLI
php bin/console make:cli-command Device SyncDevices
```

---

### 3. Bonnes Pratiques

#### Value Objects
- Toujours valider dans le constructeur avec `Assert`
- Implémenter `equals()` pour comparaison
- Implémenter `__toString()` pour affichage

#### Commands/Queries
- Toujours `readonly`
- Propriétés publiques pour faciliter l'accès
- Pas de logique métier

#### Handlers
- Toujours `readonly`
- Injection de dépendances via constructeur
- Attribut `#[AsMessageHandler]` pour découverte automatique

#### Repositories
- Interface dans `Domain/Repository/`
- Implémentation ORM dans `Infrastructure/Persistence/Doctrine/ORM/`
- Méthodes métier uniquement (pas de `findOneBy` générique)

#### Events
- Classes readonly et immutables
- Nommage au passé: `UserCreated`, `DeviceStatusChanged`
- Contenir uniquement les données nécessaires

---

## Configuration

### 1. Enregistrement des DBAL Types

Après génération d'un model, enregistrer le DBAL Type:

```yaml
# config/packages/doctrine.yaml
doctrine:
    dbal:
        types:
            device_id: Marvin\Device\Infrastructure\Persistence\Doctrine\DBAL\Types\DeviceIdType
            user_id: Marvin\Security\Infrastructure\Persistence\Doctrine\DBAL\Types\UserIdType
```

### 2. Autowiring des Repositories

```yaml
# config/services.yaml
services:
    # Repositories
    Marvin\Device\Domain\Repository\DeviceRepositoryInterface:
        alias: Marvin\Device\Infrastructure\Persistence\Doctrine\ORM\DeviceOrmRepository
```

### 3. Foundry Configuration

```yaml
# config/packages/zenstruck_foundry.yaml
when@test:
    zenstruck_foundry:
        auto_refresh_proxies: false
```

---

## Avantages du Bundle

### 1. Productivité
- Génération rapide de code boilerplate
- Respect automatique des conventions DDD
- Réduction des erreurs de structure

### 2. Cohérence
- Tous les développeurs utilisent la même structure
- Naming conventions appliquées automatiquement
- Patterns DDD/CQRS respectés

### 3. Éducatif
- Code généré avec commentaires et exemples
- Patterns détectés automatiquement (Get, List, Search)
- Instructions post-génération

### 4. Maintenabilité
- Structure claire et prévisible
- Séparation des concerns (Domain, Application, Infrastructure)
- Tests facilités avec Foundry Factories

### 5. Évolutivité
- Bounded contexts isolés
- Communication via événements
- Architecture modulaire

---

## Exemples de Workflows Complets

### Workflow 1: Création d'un nouvel agrégat Device

```bash
# 1. Créer le bounded context
php bin/console make:bounded-context Device

# 2. Créer le model Device
php bin/console make:model Device Device
# Ajouter champs: name (DeviceName), status (DeviceStatus), createdAt (datetime_immutable)

# 3. Créer les value objects
php bin/console make:value-object Device DeviceName class
php bin/console make:value-object Device DeviceStatus enum

# 4. Enregistrer le DBAL Type
# Éditer config/packages/doctrine.yaml

# 5. Créer la migration
php bin/console doctrine:migrations:diff

# 6. Créer les commandes
php bin/console make:application-command Device RegisterDevice
php bin/console make:application-command Device UpdateDeviceStatus

# 7. Créer les queries
php bin/console make:query Device GetDeviceById
php bin/console make:query Device ListDevices

# 8. Créer les fixtures
php bin/console make:fixture Device Device

# 9. Charger les fixtures
php bin/console doctrine:fixtures:load
```

### Workflow 2: Ajout d'un EventHandler inter-contexts

```bash
# Contexte: Envoyer une notification quand un device passe offline

# 1. Créer l'événement dans Device
# Manuellement: src/Device/Domain/Event/DeviceWentOffline.php

# 2. Créer le handler dans Notification
php bin/console make:event-handler Notification DeviceWentOffline
# Context source: Device
# Dépendances: NotificationServiceInterface

# 3. Le handler sera automatiquement appelé quand l'événement est dispatché
```

---

## Dépendances

- PHP 8.4+
- Symfony 7.3+
- symfony/maker-bundle
- doctrine/orm
- doctrine/doctrine-bundle
- zenstruck/foundry (pour fixtures)

---

## Ressources

### Documentation externe
- [Symfony MakerBundle](https://symfony.com/bundles/SymfonyMakerBundle/current/index.html)
- [Doctrine ORM](https://www.doctrine-project.org/projects/orm.html)
- [Zenstruck Foundry](https://github.com/zenstruck/foundry)

---

**Dernière mise à jour:** 2025-10-24
**Auteur:** Documentation technique générée pour l'équipe de développement Marvin
