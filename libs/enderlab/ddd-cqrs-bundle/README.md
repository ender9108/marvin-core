# EnderLab DDD/CQRS Bundle

Un bundle Symfony visant à faciliter l’architecture DDD/CQRS.
Il utilise Symfony Messenger pour distribuer les messages.
Un bus de commandes, un bus de requêtes, un bus d’événements de domaine, 
des attributs pour l’auto‑configuration des handlers, 
et une intégration avec Doctrine et RabbitMQ (AMQP).

Ce bundle fournit :
- Des interfaces et bus pour Command et Query.
- Un bus d’événements de domaine et une intégration AMQP avec routage par routing key.
- Des attributs pour déclarer les handlers de manière simple.
- Un écouteur Doctrine pour collecter et publier automatiquement les Domain Events depuis vos agrégats.

## Sommaire
- Installation
- Configuration
- Buses Messenger et transports
- Domain model, agrégats et Domain Events
- Commandes (Command) et Requêtes (Query)
- Routage AMQP des Domain Events et Handlers
- Exemples de code

---

## Installation

1) Enregistrer le bundle (si non auto‑découvert) :

Dans config/bundles.php

return [
    // ...
    EnderLab\DddCqrsBundle\DddCqrsBundle::class => ['all' => true],
];

2) Variables d’environnement (transports Messenger) :

- MESSENGER_TRANSPORT_DSN: DSN AMQP pour les messages asynchrones (commands + domain events), ex :

MESSENGER_TRANSPORT_DSN=amqp://guest:guest@rabbitmq:5672/%2f

> Note: %2f représente le vhost « / ».

3) Composer/Symfony: Le bundle est compatible avec Symfony 6+ (utilise Attributes, AbstractBundle et Symfony Messenger).

## Configuration

Le bundle expose une configuration ddd_cqrs avec 3 paramètres :

- exchange_name (par défaut: domain.event.exchange)
- queue_name_prefix (par défaut: domain.event.)
- routing_key_pattern (par défaut: $.*.*.*)

Exemple de configuration dans config/packages/ddd_cqrs.yaml :

ddd_cqrs:
  exchange_name: 'domain.event.exchange'
  queue_name_prefix: 'domain.event.'
  routing_key_pattern: '$.*.*.*'

Ces paramètres impactent la configuration du transport Messenger domain.event et la génération des queues/bindings AMQP (voir plus bas).

## Buses Messenger et transports

Le bundle pré‑configure les éléments suivants via DddCqrsBundle::prependExtension() :

- Transports
  - domain.event: %env(MESSENGER_TRANSPORT_DSN)% (type AMQP, exchange topic)
  - query.messages: sync://
  - command.messages: %env(MESSENGER_TRANSPORT_DSN)%
  - sync.command.messages: sync://

- Buses
  - command.bus (middleware: DomainExceptionMiddleware, doctrine_transaction, validation)
  - sync.command.bus (middleware: DomainExceptionMiddleware, doctrine_transaction, validation)
  - query.bus (sync)
  - domain_event.bus (middleware: DomainEventRoutingMiddleware, validation)

- Routing (Messenger)
  - EnderLab\DddCqrsBundle\Application\Query\QueryInterface => query.messages
  - EnderLab\DddCqrsBundle\Application\Command\CommandInterface => command.messages
  - EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface => sync.command.messages
  - EnderLab\DddCqrsBundle\Domain\Event\DomainEventInterface => domain.event

Vous pouvez surcharger/compléter ces paramètres dans votre configuration de projet.

## Domain model, agrégats et Domain Events

- Base AggregateRoot: EnderLab\DddCqrsBundle\Domain\Aggregate\AggregateRoot
  - Fournit recordThat(DomainEventInterface $event) et releaseEvents(): array
  - Vos entités agrégats peuvent hériter de cette classe pour enregistrer des événements de domaine.

- Interface DomainEventInterface et classe de base AbstractDomainEvent (avec occurredOn: DateTimeImmutable)

- Publication automatique via Doctrine :
  - L’écouteur Doctrine DomainEventDispatcher (onFlush / postFlush) collecte les événements enregistrés sur vos agrégats (AggregateRoot) et les publie sur le DomainEventBus après le flush.

- Bus d’événements de domaine :
  - EnderLab\DddCqrsBundle\Domain\Event\Bus\DomainEventBus::publish(DomainEventInterface $event)

## Commandes (Command) et Requêtes (Query)

- Commandes
  - Marquez vos messages avec CommandInterface ou SyncCommandInterface.
  - Déclarez vos handlers via l’attribut AsCommandHandler ou AsSyncCommandHandler.
  - Les bus fournis: CommandBus (async) et SyncCommandBus (sync) simplifient la distribution.

- Requêtes
  - Marquez vos messages avec QueryInterface.
  - Déclarez vos handlers via l’attribut AsQueryHandler.
  - Le QueryBus fournit ask($query) et retourne le résultat du HandledStamp.

Des handlers annotés avec ces attributs sont auto‑configurés sur les bons buses grâce au bundle.

### Exceptions fournies
- Application\Exception\MissingEntityException: levée par le FindItemQueryHandler si l’entité n’est pas trouvée.
- Domain\Exception\MissingModelException: exception générique de domaine (disponible pour vos usages).

## Routage AMQP des Domain Events et Handlers

- Attribut AsDomainEvent sur la classe de l’événement : vous devez fournir une routingKey (topic AMQP). Exemple : $.system.user.created
- À la publication (producer), DomainEventRoutingMiddleware ajoute l’AmqpStamp avec cette routingKey au message sur le transport domain.event.
- À la consommation (consumer), DomainEventRoutingMiddleware vérifie la routingKey AMQP reçue et ne laisse passer que les handlers dont les routingKeys (déclarées via AsDomainEventHandler) contiennent cette clé.
- Le Compiler Pass DomainEventMessengerCompilerPass inspecte toutes les classes taguées AsDomainEvent et calcule automatiquement, côté transport Messenger, la liste des queues et leurs binding_keys en fonction des routingKey déclarées.
  - Le nom de la queue est dérivé du préfixe de la routing key (sans le dernier segment), en remplaçant le préfixe $. par domain.event. Par exemple :
    - routingKey: $.system.user.created → queue: domain.event.system.user
    - binding_keys ajoutés: $.system.user.created (et variantes si vous utilisez * pour l’événement)
  - Si vous utilisez $.system.user.* comme routingKey, le bundle générera les bindings pour les types connus: created, updated, deleted.

- Écrivez vos handlers d’événements de domaine avec AsDomainEventHandler(routingKeys: ['$.system.user.created', ...]). Ils sont rattachés automatiquement au bus domain_event.bus et filtrés par routing key.


## Exemples de code

### 1) Domain Event

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;
use EnderLab\DddCqrsBundle\Domain\Event\Attribute\AsDomainEvent;

#[AsDomainEvent(routingKey: '$.system.user.created')]
final class UserCreated extends AbstractDomainEvent
{
    public function __construct(public readonly int $userId) { parent::__construct(); }
}

### 2) AggregateRoot qui enregistre un événement

use EnderLab\DddCqrsBundle\Domain\Aggregate\AggregateRoot;

class User extends AggregateRoot
{
    public static function create(int $id): self
    {
        $self = new self();
        $self->recordThat(new UserCreated($id));
        return $self;
    }
}

Grâce au DomainEventDispatcher, l’événement sera publié après le flush Doctrine.

### 3) Handler pour l’événement (consommation)

use EnderLab\DddCqrsBundle\Domain\Event\Attribute\AsDomainEventHandler;

#[AsDomainEventHandler(routingKeys: ['$.system.user.created'])]
final class WhenUserCreated
{
    public function __invoke(UserCreated $event): void
    {
        // Réagir à l’événement
    }
}

### 4) Command + Handler

use EnderLab\DddCqrsBundle\Application\Command\CommandInterface;
use EnderLab\DddCqrsBundle\Application\Command\Attribute\AsCommandHandler;

final class RegisterUser implements CommandInterface
{
    public function __construct(public string $email) {}
}

#[AsCommandHandler]
final class RegisterUserHandler
{
    public function __invoke(RegisterUser $command): void
    {
        // logique d’inscription (async par défaut)
    }
}

Dispatch :

use EnderLab\DddCqrsBundle\Application\Command\Bus\CommandBus;

$bus->dispatch(new RegisterUser('john@doe.tld'));

### 5) Query + Handler

use EnderLab\DddCqrsBundle\Application\Query\QueryInterface;
use EnderLab\DddCqrsBundle\Application\Query\Attribute\AsQueryHandler;
use EnderLab\DddCqrsBundle\Application\Query\Bus\QueryBus;

final class GetUser implements QueryInterface
{
    public function __construct(public int $id) {}
}

#[AsQueryHandler]
final class GetUserHandler
{
    public function __invoke(GetUser $query): array
    {
        return ['id' => $query->id, 'email' => 'john@doe.tld'];
    }
}

$dto = $queryBus->ask(new GetUser(1));

### 6) Command synchrone

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use EnderLab\DddCqrsBundle\Application\Command\Attribute\AsSyncCommandHandler;
use EnderLab\DddCqrsBundle\Application\Command\Bus\SyncCommandBus;

final class ComputeSomething implements SyncCommandInterface
{
    public function __construct(public int $a, public int $b) {}
}

#[AsSyncCommandHandler]
final class ComputeSomethingHandler
{
    public function __invoke(ComputeSomething $command): int
    {
        return $command->a + $command->b;
    }
}

$result = $syncCommandBus->dispatch(new ComputeSomething(1, 2)); // 3

## Notes & bonnes pratiques

- Utilisez des routing keys explicites pour vos Domain Events. Le schéma recommandé est: $.<boundedContext>.<aggregate>.<eventType> (eventType parmi created|updated|deleted ou *).
- Les handlers d’événements devraient être idempotents et tolérants aux relectures.
- Les Command/Query sont intentionnellement simples (Message + Handler) ; la validation/transactions sont gérées via middleware.
- Le middleware DomainExceptionMiddleware capture et logge les DomainException sur les buses de commandes.

## Licence

Voir le fichier LICENSE à la racine du projet.
