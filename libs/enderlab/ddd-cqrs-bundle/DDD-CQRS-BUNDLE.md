# DDD-CQRS-BUNDLE - Technical Documentation

## Vue d'ensemble

Le **ddd-cqrs-bundle** est une bibliothèque Symfony qui fournit les fondations pour implémenter l'architecture DDD (Domain-Driven Design) avec le pattern CQRS (Command Query Responsibility Segregation). Elle offre des abstractions pour les commandes, queries, événements de domaine, et intègre Symfony Messenger pour le routing des messages.

## Architecture

### Structure du bundle

```
src/
├── Application/
│   ├── Command/         # Interfaces pour les commandes (write operations)
│   ├── Query/           # Interfaces pour les queries (read operations)
│   └── Event/           # Interfaces et traits pour les événements de domaine
├── Domain/
│   ├── Model/           # Classes de base pour les agrégats
│   ├── Assert/          # Utilitaires de validation
│   ├── Event/           # Interfaces et classes de base pour les événements
│   ├── Exception/       # Exceptions métier de base
│   ├── Repository/      # Interfaces de repository
│   └── ValueObject/     # Interfaces pour les value objects
└── Infrastructure/
    ├── Persistence/Doctrine/
    │   ├── DomainEventDispatcher/  # Dispatcher d'événements Doctrine
    │   └── ORM/                     # Paginator ORM
    └── Framework/Symfony/
        └── Messenger/Bus/           # Implémentations des bus avec Messenger
```

---

## Application Layer

### 1. Commands (Write Operations)

#### CommandInterface
**Fichier:** `src/Application/Command/CommandInterface.php`

Interface marker pour les commandes **asynchrones**.

```php
interface CommandInterface {}
```

**Usage:**
```php
final readonly class CreateUser implements CommandInterface
{
    public function __construct(
        public Email $email,
        public string $password,
    ) {}
}
```

**Caractéristiques:**
- Commandes asynchrones (via Symfony Messenger)
- Pas de valeur de retour
- Peuvent être mises en file d'attente
- Routing via `commands` bus

---

#### SyncCommandInterface
**Fichier:** `src/Application/Command/SyncCommandInterface.php`

Interface marker pour les commandes **synchrones**.

```php
interface SyncCommandInterface {}
```

**Usage:**
```php
final readonly class UpdateUserProfile implements SyncCommandInterface
{
    public function __construct(
        public UserId $userId,
        public string $firstname,
    ) {}
}
```

**Caractéristiques:**
- Commandes synchrones (exécution immédiate)
- Peuvent retourner une valeur
- Pas de mise en file d'attente
- Routing direct

---

#### CommandHandlerInterface
**Fichier:** `src/Application/Command/CommandHandlerInterface.php`

Interface pour les handlers de commandes asynchrones.

```php
interface CommandHandlerInterface {}
```

**Implémentation:**
```php
#[AsMessageHandler]
final readonly class CreateUserHandler implements CommandHandlerInterface
{
    public function __invoke(CreateUser $command): void
    {
        // Logique métier
    }
}
```

---

#### SyncCommandHandlerInterface
**Fichier:** `src/Application/Command/SyncCommandHandlerInterface.php`

Interface pour les handlers de commandes synchrones.

```php
interface SyncCommandHandlerInterface {}
```

**Implémentation:**
```php
#[AsMessageHandler]
final readonly class UpdateUserProfileHandler implements SyncCommandHandlerInterface
{
    public function __invoke(UpdateUserProfile $command): User
    {
        // Logique métier
        return $user;
    }
}
```

---

### 2. Queries (Read Operations)

#### QueryInterface
**Fichier:** `src/Application/Query/QueryInterface.php`

Interface marker pour les queries.

```php
interface QueryInterface {}
```

**Usage:**
```php
final readonly class GetUserById implements QueryInterface
{
    public function __construct(
        public UserId $userId,
    ) {}
}
```

**Caractéristiques:**
- Toujours synchrones
- Lecture seule (pas de modification d'état)
- Retournent toujours une valeur

---

#### QueryHandlerInterface
**Fichier:** `src/Application/Query/QueryHandlerInterface.php`

Interface pour les handlers de queries.

```php
interface QueryHandlerInterface {}
```

**Implémentation:**
```php
#[AsMessageHandler]
final readonly class GetUserByIdHandler implements QueryHandlerInterface
{
    public function __invoke(GetUserById $query): ?User
    {
        return $this->userRepository->byId($query->userId);
    }
}
```

---

### 3. Events (Domain Events)

#### DomainEventHandlerInterface
**Fichier:** `src/Application/Event/DomainEventHandlerInterface.php`

Interface pour les handlers d'événements de domaine.

```php
interface DomainEventHandlerInterface {}
```

**Usage:**
```php
#[AsMessageHandler]
final readonly class SendWelcomeEmailHandler implements DomainEventHandlerInterface
{
    public function __invoke(UserCreated $event): void
    {
        // Envoyer email de bienvenue
    }
}
```

---

## Domain Layer

### 1. AggregateRoot

**Fichier:** `src/Domain/Model/AggregateRoot.php`

Classe de base pour les agrégats DDD avec gestion des événements de domaine.

```php
abstract class AggregateRoot
{
    private array $recordedEvents = [];
    
    public function recordThat(DomainEventInterface $event): void
    {
        $this->recordedEvents[] = $event;
    }
    
    public function releaseEvents(): array
    {
        $events = $this->recordedEvents;
        $this->recordedEvents = [];
        return $events;
    }
}
```

**Usage:**
```php
final class User extends AggregateRoot
{
    public function delete(): self
    {
        $this->recordThat(new UserDeleted($this->id));
        return $this;
    }
}
```

**Pattern:**
- Les événements sont enregistrés avec `recordThat()`
- Les événements sont collectés automatiquement par `DomainEventDispatcher`
- Les événements sont dispatché après le `flush()` Doctrine

---

### 2. Domain Events

#### DomainEventInterface
**Fichier:** `src/Domain/Event/DomainEventInterface.php`

Interface marker pour les événements de domaine.

```php
interface DomainEventInterface {}
```

**Usage:**
```php
final readonly class UserCreated implements DomainEventInterface
{
    public function __construct(
        public UserId $userId,
        public Email $email,
        public DateTimeInterface $occurredOn = new DateTimeImmutable()
    ) {}
}
```

---

### 3. Assert Utilities

**Fichier:** `src/Domain/Assert/Assert.php`

Classe utilitaire fournissant 100+ méthodes de validation statiques.

**!!! Fork de Webmozart/Assert !!!**

**Catégories de validations:**

#### Types
- `string()`, `integer()`, `float()`, `boolean()`, `scalar()`
- `object()`, `resource()`, `isCallable()`, `isArray()`
- `isIterable()`, `isCountable()`

#### Strings
- `stringNotEmpty()`, `notWhitespaceOnly()`
- `startsWith()`, `endsWith()`, `contains()`
- `regex()`, `email()`, `ip()`, `ipv4()`, `ipv6()`
- `alpha()`, `alnum()`, `digits()`, `lower()`, `upper()`
- `length()`, `minLength()`, `maxLength()`, `lengthBetween()`

#### Numbers
- `positiveInteger()`, `natural()`, `numeric()`
- `greaterThan()`, `greaterThanEq()`, `lessThan()`, `lessThanEq()`
- `range()`, `eq()`, `notEq()`, `same()`, `notSame()`

#### Arrays
- `isEmpty()`, `notEmpty()`, `uniqueValues()`
- `oneOf()`, `inArray()`, `count()`, `minCount()`, `maxCount()`
- `keyExists()`, `keyNotExists()`, `isList()`, `isMap()`

#### Objects/Classes
- `isInstanceOf()`, `notInstanceOf()`, `isInstanceOfAny()`
- `isAOf()`, `isNotA()`, `classExists()`, `subclassOf()`
- `interfaceExists()`, `implementsInterface()`
- `propertyExists()`, `methodExists()`

#### Files
- `fileExists()`, `file()`, `directory()`
- `readable()`, `writable()`

#### Other
- `null()`, `notNull()`, `true()`, `false()`
- `uuid()`, `isValidTimezone()`, `throws()`

**Usage:**
```php
use EnderLab\DddCqrsBundle\Domain\Assert\Assert;

Assert::notEmpty($email);
Assert::email($email);
Assert::lengthBetween($email, 5, 255);
```

**Exception levée:** `InvalidArgument`

---

### 4. Exceptions

#### DomainException
**Fichier:** `src/Domain/Exception/DomainException.php`

Classe de base pour les exceptions métier.

```php
abstract class DomainException extends RuntimeException
{
    protected const UNKNOWN_ERROR_CODE = 'E9999';
    protected string $internalCode = self::UNKNOWN_ERROR_CODE;
    
    public function __construct(string $message, ?string $code = null)
    {
        parent::__construct($message);
        if (null !== $code) {
            $this->internalCode = $code;
        }
    }
    
    public function getInternalCode(): string
    {
        return $this->internalCode;
    }
}
```

**Usage:**
```php
final class UserNotFound extends DomainException
{
    public static function withId(UserId $id): self
    {
        return new self(
            "User with id {$id->toString()} not found",
            'E1001'
        );
    }
}
```

---

#### InvalidArgument
**Fichier:** `src/Domain/Exception/InvalidArgument.php`

Exception levée par les assertions `Assert`.

---

#### TranslatableExceptionInterface
**Fichier:** `src/Domain/Exception/TranslatableExceptionInterface.php`

Interface pour les exceptions traduisibles.

```php
interface TranslatableExceptionInterface
{
    public function translationId(): string;
    public function translationParameters(): array;
    public function translationDomain(): string;
}
```

---

### 5. Repositories

#### RepositoryInterface
**Fichier:** `src/Domain/Repository/RepositoryInterface.php`

Interface de base pour les repositories.

---

#### PaginatorInterface
**Fichier:** `src/Domain/Repository/PaginatorInterface.php`

Interface pour la pagination.

**Implémentation Doctrine:** `PaginatorOrm`

---

### 6. Value Objects

#### ValueObjectInterface
**Fichier:** `src/Domain/ValueObject/ValueObjectInterface.php`

Interface marker pour les value objects.

```php
interface ValueObjectInterface {}
```

---

## Infrastructure Layer

### 1. Symfony Messenger Buses

#### MessengerCommandBus
**Fichier:** `src/Infrastructure/Framework/Symfony/Messenger/Bus/MessengerCommandBus.php`

Bus pour les commandes **asynchrones**.

```php
final readonly class MessengerCommandBus implements CommandBusInterface
{
    public function dispatch(CommandInterface $command): void
    {
        $this->messageBus->dispatch(
            $command,
            [new BusNameStamp('commands')]
        );
    }
}
```

**Caractéristiques:**
- Dispatch asynchrone via Messenger
- Pas de retour de valeur
- Routing via le bus `commands`

**Configuration:**
```yaml
# config/packages/messenger.yaml
framework:
    messenger:
        buses:
            messenger.bus.commands:
                middleware:
                    - doctrine_transaction
```

---

#### MessengerSyncCommandBus
**Fichier:** `src/Infrastructure/Framework/Symfony/Messenger/Bus/MessengerSyncCommandBus.php`

Bus pour les commandes **synchrones**.

```php
final class MessengerSyncCommandBus implements SyncCommandBusInterface
{
    use HandleTrait;
    
    public function handle(SyncCommandInterface $message): mixed
    {
        try {
            return $this->messengerHandle($message);
        } catch (HandlerFailedException $e) {
            // Unwrap exception
            while ($e instanceof HandlerFailedException) {
                $e = $e->getPrevious();
            }
            throw $e;
        }
    }
}
```

**Caractéristiques:**
- Exécution synchrone immédiate
- Retourne la valeur du handler
- Unwrap automatique des `HandlerFailedException`

---

#### MessengerQueryBus
**Fichier:** `src/Infrastructure/Framework/Symfony/Messenger/Bus/MessengerQueryBus.php`

Bus pour les queries.

**Caractéristiques:**
- Toujours synchrone
- Retourne toujours une valeur
- Unwrap automatique des exceptions

---

#### MessengerDomainEventBus
**Fichier:** `src/Infrastructure/Framework/Symfony/Messenger/Bus/MessengerDomainEventBus.php`

Bus pour les événements de domaine.

**Caractéristiques:**
- Peut être synchrone ou asynchrone selon configuration
- Routing via le bus `domain_events`

---

### 2. Doctrine Integration

#### DomainEventDispatcher
**Fichier:** `src/Infrastructure/Persistence/Doctrine/DomainEventDispatcher/DomainEventDispatcher.php`

Dispatcher automatique des événements de domaine après les transactions Doctrine.

```php
#[AsDoctrineListener(event: Events::onFlush)]
#[AsDoctrineListener(event: Events::postFlush)]
final class DomainEventDispatcher
{
    private array $eventsToDispatch = [];
    
    public function onFlush(OnFlushEventArgs $args): void
    {
        $uow = $args->getObjectManager()->getUnitOfWork();
        $models = array_merge(
            $uow->getScheduledEntityInsertions(),
            $uow->getScheduledEntityUpdates(),
            $uow->getScheduledEntityDeletions()
        );
        
        foreach ($models as $model) {
            if (!$model instanceof AggregateRoot) {
                continue;
            }
            foreach ($model->releaseEvents() as $event) {
                $this->eventsToDispatch[] = $event;
            }
        }
    }
    
    public function postFlush(PostFlushEventArgs $args): void
    {
        foreach ($this->eventsToDispatch as $event) {
            $this->domainEventBus->dispatch($event);
        }
        $this->eventsToDispatch = [];
    }
}
```

**Pattern:**
1. **onFlush**: Collecte les événements des agrégats modifiés
2. **postFlush**: Dispatche les événements **après** le commit de la transaction
3. Garantit que les événements ne sont dispatché que si la transaction réussit

---

## Patterns et Best Practices

### 1. CQRS Strict

**Commands (Write):**
- Modifient l'état du système
- Retournent `void` ou l'entité modifiée
- Peuvent être asynchrones ou synchrones

**Queries (Read):**
- Ne modifient jamais l'état
- Toujours synchrones
- Retournent toujours des données

**Events:**
- Notifications de changements d'état
- Pas de logique métier
- Peuvent être écoutés par plusieurs handlers

---

### 2. Commandes Synchrones vs Asynchrones

**Synchrone (`SyncCommandInterface`):**
- Besoin d'un retour immédiat
- Validation critique
- Transactions courtes
- Exemple: Login, création d'utilisateur

**Asynchrone (`CommandInterface`):**
- Pas de retour nécessaire
- Traitement long
- Peut échouer sans bloquer l'utilisateur
- Exemple: Envoi d'email, génération de rapport

---

### 3. Gestion des Événements de Domaine

**Pattern:**
```php
// 1. L'agrégat enregistre l'événement
final class User extends AggregateRoot
{
    public function changeEmail(Email $newEmail): void
    {
        $oldEmail = $this->email;
        $this->email = $newEmail;
        
        $this->recordThat(new UserEmailChanged(
            $this->id,
            $oldEmail,
            $newEmail
        ));
    }
}

// 2. Save de l'agrégat
$user->changeEmail(new Email('new@example.com'));
$this->userRepository->save($user);
// flush() automatique via Doctrine

// 3. DomainEventDispatcher collecte et dispatche les événements
// Après le commit de la transaction

// 4. Handlers réagissent à l'événement
#[AsMessageHandler]
final readonly class NotifyUserEmailChangedHandler implements DomainEventHandlerInterface
{
    public function __invoke(UserEmailChanged $event): void
    {
        // Envoyer notification
    }
}
```

**Avantages:**
- Découplage total entre agrégats
- Communication inter-bounded-contexts
- Garantie transactionnelle (événements dispatché seulement si commit réussit)

---

### 4. Validation avec Assert

**Dans les Value Objects:**
```php
final readonly class Email implements ValueObjectInterface
{
    public string $value;
    
    public function __construct(string $email)
    {
        Assert::notEmpty($email);
        Assert::email($email);
        Assert::lengthBetween($email, 5, 255);
        
        $this->value = $email;
    }
}
```

**Dans les Handlers:**
```php
public function __invoke(CreateUser $command): User
{
    // Validation métier
    Assert::notNull($command->email);
    $this->uniqueEmailVerifier->verify($command->email);
    
    // Création
    $user = User::create($command->email, ...);
    $this->userRepository->save($user);
    
    return $user;
}
```

---

### 5. Exceptions Métier

**Définition:**
```php
final class EmailAlreadyUsed extends DomainException implements TranslatableExceptionInterface
{
    private const ERROR_CODE = 'E1010';
    
    public function __construct(Email $email)
    {
        parent::__construct(
            "Email {$email->value} is already used",
            self::ERROR_CODE
        );
        $this->email = $email;
    }
    
    public function translationId(): string
    {
        return 'security.exception.email_already_used';
    }
    
    public function translationParameters(): array
    {
        return ['email' => $this->email->value];
    }
    
    public function translationDomain(): string
    {
        return 'exceptions';
    }
}
```

---

## Configuration Symfony

### 1. Services

```yaml
# config/services.yaml
services:
    # Command Bus Async
    EnderLab\DddCqrsBundle\Application\Command\CommandBusInterface:
        alias: EnderLab\DddCqrsBundle\Infrastructure\Framework\Symfony\Messenger\Bus\MessengerCommandBus
    
    # Command Bus Sync
    EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface:
        alias: EnderLab\DddCqrsBundle\Infrastructure\Framework\Symfony\Messenger\Bus\MessengerSyncCommandBus
    
    # Query Bus
    EnderLab\DddCqrsBundle\Application\Query\QueryBusInterface:
        alias: EnderLab\DddCqrsBundle\Infrastructure\Framework\Symfony\Messenger\Bus\MessengerQueryBus
    
    # Event Bus
    EnderLab\DddCqrsBundle\Application\Event\DomainEventBusInterface:
        alias: EnderLab\DddCqrsBundle\Infrastructure\Framework\Symfony\Messenger\Bus\MessengerDomainEventBus
```

### 2. Messenger

```yaml
# config/packages/messenger.yaml
framework:
    messenger:
        default_bus: messenger.bus.commands
        
        buses:
            messenger.bus.commands:
                middleware:
                    - doctrine_transaction
                    
            messenger.bus.queries:
                middleware: []
                
            messenger.bus.domain_events:
                default_middleware: allow_no_handlers
                middleware:
                    - doctrine_transaction
        
        transports:
            async: '%env(MESSENGER_TRANSPORT_DSN)%'
        
        routing:
            'EnderLab\DddCqrsBundle\Application\Command\CommandInterface': async
            'EnderLab\DddCqrsBundle\Domain\Event\DomainEventInterface': async
```

---

## Usage Examples

### Dispatch Command Async

```php
use EnderLab\DddCqrsBundle\Application\Command\CommandBusInterface;

final readonly class UserController
{
    public function __construct(
        private CommandBusInterface $commandBus
    ) {}
    
    public function create(Request $request): Response
    {
        $command = new CreateUser(
            new Email($request->get('email')),
            $request->get('password')
        );
        
        $this->commandBus->dispatch($command);
        
        return new Response('User creation queued', 202);
    }
}
```

### Execute Command Sync

```php
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;

final readonly class UserService
{
    public function __construct(
        private SyncCommandBusInterface $commandBus
    ) {}
    
    public function updateProfile(UserId $userId, array $data): User
    {
        $command = new UpdateUserProfile($userId, $data);
        
        return $this->commandBus->handle($command);
    }
}
```

### Execute Query

```php
use EnderLab\DddCqrsBundle\Application\Query\QueryBusInterface;

final readonly class UserController
{
    public function __construct(
        private QueryBusInterface $queryBus
    ) {}
    
    public function show(string $id): Response
    {
        $query = new GetUserById(new UserId($id));
        $user = $this->queryBus->ask($query);
        
        return $this->json($user);
    }
}
```

---

## Testing

### Mock Command Bus

```php
use PHPUnit\Framework\TestCase;
use EnderLab\DddCqrsBundle\Application\Command\CommandBusInterface;

class UserServiceTest extends TestCase
{
    public function testCreateUser(): void
    {
        $commandBus = $this->createMock(CommandBusInterface::class);
        $commandBus->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(CreateUser::class));
        
        $service = new UserService($commandBus);
        $service->createUser('test@example.com', 'password');
    }
}
```

---

## Migration depuis un bundle précédent

Si vous migrez depuis une version custom ou un autre bundle CQRS:

1. Remplacer les interfaces de bus par celles de ce bundle
2. Adapter les handlers pour utiliser `#[AsMessageHandler]`
3. Étendre `AggregateRoot` pour les agrégats
4. Utiliser `recordThat()` pour les événements de domaine
5. Configurer Messenger selon vos besoins (sync/async)

---

## Dépendances

- PHP 8.4+
- Symfony 7.3+
- Doctrine ORM 3+
- Symfony Messenger

---

## Ressources

### Documentation externe
- [Symfony Messenger](https://symfony.com/doc/current/messenger.html)
- [DDD Patterns](https://martinfowler.com/bliki/DomainDrivenDesign.html)
- [CQRS Pattern](https://martinfowler.com/bliki/CQRS.html)

---

**Dernière mise à jour:** 2025-10-24
**Auteur:** Documentation technique générée pour l'équipe de développement Marvin
