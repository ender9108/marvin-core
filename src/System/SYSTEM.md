# Bounded Context: System - Documentation Technique

## Vue d'ensemble

Le bounded context **System** gère l'infrastructure système de Marvin : les conteneurs Docker et les workers Supervisord. Il permet de monitorer, contrôler et synchroniser l'état des composants système nécessaires au fonctionnement de la plateforme domotique.

**Responsabilités principales :**
- Gestion du cycle de vie des conteneurs Docker (start, stop, restart, build, exec)
- Monitoring des workers Supervisord (consumers, protocols, cron, monitors)
- Suivi des requêtes d'actions asynchrones (ActionRequest)
- Synchronisation de l'état système avec les services externes

---

## Table des matières

1. [Domain Models](#1-domain-models)
   - [Container](#container)
   - [Worker](#worker)
   - [ActionRequest](#actionrequest)
2. [Value Objects](#2-value-objects)
   - [ContainerStatus](#containerstatus)
   - [ContainerType](#containertype)
   - [ContainerImage](#containerimage)
   - [ContainerAllowedActions](#containerallowedactions)
   - [WorkerStatus](#workerstatus)
   - [WorkerType](#workertype)
   - [WorkerAllowedActions](#workerallowedactions)
   - [ActionStatus](#actionstatus)
   - [SupervisorProcess](#supervisorprocess)
3. [Application Layer](#3-application-layer)
   - [Commands & CommandHandlers](#commands--commandhandlers)
   - [Queries & QueryHandlers](#queries--queryhandlers)
   - [EventHandlers](#eventhandlers)
4. [Domain Events](#4-domain-events)
5. [Infrastructure](#5-infrastructure)
6. [Presentation](#6-presentation)
7. [Patterns & Best Practices](#7-patterns--best-practices)

---

## 1. Domain Models

### Container

**Fichier:** `src/System/Domain/Model/Container.php`

Représente un conteneur Docker géré par Marvin.

**Propriétés:**
- `ContainerId $id` - Identité unique (readonly)
- `Label $serviceLabel` - Nom du service (ex: "zigbee2mqtt")
- `ContainerType $type` - Type de conteneur (protocol, database, broker, monitoring, mailer)
- `ContainerStatus $status` - État actuel (running, stopped, paused, restarting, exited, unknown)
- `ContainerAllowedActions $allowedActions` - Actions autorisées sur ce conteneur
- `?string $containerId` - ID Docker du conteneur
- `?string $containerLabel` - Label Docker du conteneur
- `?ContainerImage $image` - Image Docker (nom:tag)
- `array $ports` - Mapping des ports
- `array $volumes` - Mapping des volumes
- `?Metadata $metadata` - Métadonnées supplémentaires
- `?DateTimeInterface $lastSyncedAt` - Date de dernière synchronisation
- `DateTimeInterface $createdAt` - Date de création

**Méthodes:**
```php
$container->isActionAllowed(string $action): bool
$container->updateStatus(ContainerStatus $status): void
```

**Usage:**
```php
use Marvin\System\Domain\Model\Container;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\System\Domain\ValueObject\{ContainerType, ContainerStatus, ContainerAllowedActions};

$container = new Container(
    serviceLabel: new Label('zigbee2mqtt'),
    type: ContainerType::PROTOCOL,
    status: ContainerStatus::RUNNING,
    allowedActions: new ContainerAllowedActions(['start', 'stop', 'restart']),
);

if ($container->isActionAllowed('restart')) {
    // Effectuer le restart
}

$container->updateStatus(ContainerStatus::STOPPED);
```

---

### Worker

**Fichier:** `src/System/Domain/Model/Worker.php`

Représente un worker/process Supervisord.

**Propriétés:**
- `WorkerId $id` - Identité unique (readonly)
- `Label $label` - Nom du worker (ex: "messenger-consume-async")
- `WorkerType $type` - Type de worker (consumer, protocol, cron, monitor, unknown)
- `string $command` - Commande exécutée par le worker
- `WorkerAllowedActions $allowedActions` - Actions autorisées (start, stop, restart)
- `?int $numProcs` - Nombre de processus
- `?string $uptime` - Temps de fonctionnement
- `?WorkerStatus $status` - État actuel
- `?Metadata $metadata` - Métadonnées supplémentaires
- `?DateTimeInterface $lastSyncedAt` - Date de dernière synchronisation
- `DateTimeInterface $createdAt` - Date de création

**Usage:**
```php
use Marvin\System\Domain\Model\Worker;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\System\Domain\ValueObject\{WorkerType, WorkerStatus, WorkerAllowedActions};

$worker = new Worker(
    label: new Label('messenger-consume-async'),
    type: WorkerType::CONSUMER,
    command: 'php bin/console messenger:consume async',
    allowedActions: new WorkerAllowedActions(['start', 'stop', 'restart']),
    status: WorkerStatus::RUNNING,
);
```

---

### ActionRequest

**Fichier:** `src/System/Domain/Model/ActionRequest.php`

Représente une demande d'action asynchrone sur une entité (Container ou Worker).

**Propriétés:**
- `ActionRequestId $id` - Identité unique (readonly)
- `UniqId $correlationId` - ID de corrélation pour tracer l'action
- `string $entityType` - Type d'entité cible ("container" ou "worker")
- `string $entityId` - ID de l'entité cible
- `string $action` - Action demandée (start, stop, restart, build, exec)
- `ActionStatus $status` - État de la requête (pending, completed, failed, timeout)
- `array $input` - Données d'entrée de l'action
- `?string $output` - Sortie de l'action
- `?string $error` - Message d'erreur en cas d'échec
- `?DateTimeInterface $completedAt` - Date de complétion
- `DateTimeInterface $createdAt` - Date de création

**Méthodes:**
```php
$actionRequest->markAsCompleted(bool $success, ?string $output = null, ?string $error = null): void
$actionRequest->markAsTimeout(): void
```

**Usage:**
```php
use Marvin\System\Domain\Model\ActionRequest;
use Marvin\Shared\Domain\ValueObject\Identity\UniqId;
use Marvin\System\Domain\ValueObject\ActionStatus;

$actionRequest = new ActionRequest(
    correlationId: new UniqId(),
    entityType: 'container',
    entityId: $containerId->toString(),
    action: 'restart',
    status: ActionStatus::PENDING,
    input: ['timeout' => 30],
);

// Marquer comme complétée
$actionRequest->markAsCompleted(
    success: true,
    output: 'Container restarted successfully'
);

// Ou marquer comme timeout
$actionRequest->markAsTimeout();
```

**Règles métier:**
- Une ActionRequest est créée pour chaque action asynchrone
- Le `correlationId` permet de tracer l'action de bout en bout
- Les actions peuvent expirer (timeout) si pas complétées dans le délai imparti
- Une fois complétée, l'ActionRequest ne peut plus être modifiée

---

## 2. Value Objects

### ContainerStatus

**Fichier:** `src/System/Domain/ValueObject/ContainerStatus.php`

Énumération des états possibles d'un conteneur Docker.

**États disponibles:**
- `RUNNING` - Conteneur en cours d'exécution
- `STOPPED` - Conteneur arrêté
- `PAUSED` - Conteneur en pause
- `RESTARTING` - Conteneur en cours de redémarrage
- `EXITED` - Conteneur terminé
- `UNKNOWN` - État inconnu

**API:**
```php
// Vérifications d'état
$status->isRunning(): bool
$status->isStopped(): bool
$status->isExited(): bool
$status->isPaused(): bool
$status->isRestarting(): bool

// Factory
ContainerStatus::RUNNING
ContainerStatus::STOPPED
```

**Usage:**
```php
use Marvin\System\Domain\ValueObject\ContainerStatus;

$status = ContainerStatus::RUNNING;

if ($status->isRunning()) {
    // Le conteneur est actif
}

// Comparaison
if ($status->equals(ContainerStatus::RUNNING)) {
    // États identiques
}
```

---

### ContainerType

**Fichier:** `src/System/Domain/ValueObject/ContainerType.php`

Énumération des types de conteneurs gérés par Marvin.

**Types disponibles:**
- `PROTOCOL` - Conteneurs de protocoles domotiques (Zigbee2MQTT, Matter, etc.)
- `DATABASE` - Bases de données (PostgreSQL, TimescaleDB, etc.)
- `BROKER` - Message brokers (MQTT, RabbitMQ, etc.)
- `MONITORING` - Outils de monitoring (Prometheus, Grafana, etc.)
- `MAILER` - Services de messagerie (MailHog, etc.)

**API:**
```php
$type->isProtocol(): bool
$type->isDatabase(): bool
$type->isBroker(): bool
$type->isMonitoring(): bool
$type->isMailer(): bool
```

**Usage:**
```php
use Marvin\System\Domain\ValueObject\ContainerType;

$type = ContainerType::PROTOCOL;

if ($type->isProtocol()) {
    // Appliquer une logique spécifique aux protocoles
}
```

---

### ContainerImage

**Fichier:** `src/System/Domain/ValueObject/ContainerImage.php`

Représente une image Docker avec validation du format `nom:tag`.

**Validation:**
- Format requis: `nom:tag`
- Regex: `/^[a-z0-9\/\-_]+:[a-z0-9\.\-_]+$/i`

**Méthodes:**
```php
$image->getName(): string  // Partie avant ':'
$image->getTag(): string   // Partie après ':'
$image->toString(): string // Image complète
```

**Usage:**
```php
use Marvin\System\Domain\ValueObject\ContainerImage;

$image = new ContainerImage('koenkk/zigbee2mqtt:1.35.0');

echo $image->getName();  // "koenkk/zigbee2mqtt"
echo $image->getTag();   // "1.35.0"
echo $image->toString(); // "koenkk/zigbee2mqtt:1.35.0"
```

---

### ContainerAllowedActions

**Fichier:** `src/System/Domain/ValueObject/ContainerAllowedActions.php`

Liste des actions autorisées sur un conteneur.

**Validation:**
- Les actions doivent être présentes dans `ManagerActionReference::values()`
- Le tableau ne peut pas être vide

**API:**
```php
$allowedActions->toArray(): array
$allowedActions->equals(ContainerAllowedActions $other): bool
```

**Usage:**
```php
use Marvin\System\Domain\ValueObject\ContainerAllowedActions;

$actions = new ContainerAllowedActions(['start', 'stop', 'restart']);

if (in_array('restart', $actions->toArray())) {
    // Le restart est autorisé
}
```

---

### WorkerStatus

**Fichier:** `src/System/Domain/ValueObject/WorkerStatus.php`

Énumération des états Supervisord pour les workers.

**États disponibles:**
- `STOPPED` - Process arrêté
- `STARTING` - Process en cours de démarrage
- `RUNNING` - Process en cours d'exécution (état sain)
- `BACKOFF` - Process en mode backoff après échec de démarrage
- `STOPPING` - Process en cours d'arrêt
- `EXITED` - Process terminé (peut redémarrer si autorestart)
- `FATAL` - Process en échec définitif (trop de tentatives)
- `UNKNOWN` - État inconnu

**Méthodes de vérification:**
```php
$status->isHealthy(): bool        // RUNNING uniquement
$status->isRunning(): bool
$status->isStopped(): bool
$status->isTransitional(): bool   // STARTING ou STOPPING
$status->isError(): bool          // BACKOFF ou FATAL

// Vérifications d'actions autorisées
$status->canStart(): bool         // STOPPED, EXITED, FATAL
$status->canStop(): bool          // RUNNING, BACKOFF
$status->canRestart(): bool       // Tous sauf STARTING, STOPPING
```

**Méthodes UI:**
```php
$status->getColor(): string  // green, yellow, gray, red, cyan, magenta
$status->getIcon(): string   // ✓, ⟳, ⏸, ■, ⚠, ✗, ○, ?
```

**Usage:**
```php
use Marvin\System\Domain\ValueObject\WorkerStatus;

$status = WorkerStatus::RUNNING;

if ($status->isHealthy()) {
    // Le worker fonctionne correctement
}

if ($status->canRestart()) {
    // Le restart est possible
}

// Pour l'affichage CLI
echo $status->getIcon() . ' ' . $status->value; // "✓ running"
```

---

### WorkerType

**Fichier:** `src/System/Domain/ValueObject/WorkerType.php`

Énumération des types de workers Supervisord.

**Types disponibles:**
- `CONSUMER` - Workers consommant des messages (Symfony Messenger)
- `PROTOCOL` - Workers gérant des protocoles domotiques
- `CRON` - Workers exécutant des tâches planifiées
- `MONITOR` - Workers de monitoring
- `UNKNOWN` - Type inconnu

**API:**
```php
WorkerType::fromString(string $value): self

$type->isConsumer(): bool
$type->isProtocol(): bool
$type->isCron(): bool
$type->isMonitor(): bool
$type->isUnknown(): bool
```

**Usage:**
```php
use Marvin\System\Domain\ValueObject\WorkerType;

$type = WorkerType::CONSUMER;

if ($type->isConsumer()) {
    // Appliquer une logique spécifique aux consumers
}

// Parsing depuis une string
$type = WorkerType::fromString('consumer');
```

---

### WorkerAllowedActions

**Fichier:** `src/System/Domain/ValueObject/WorkerAllowedActions.php`

Liste des actions autorisées sur un worker.

**Actions possibles:**
- `start` - Démarrer le worker
- `stop` - Arrêter le worker
- `restart` - Redémarrer le worker

**Usage:**
```php
use Marvin\System\Domain\ValueObject\WorkerAllowedActions;

$actions = new WorkerAllowedActions(['start', 'stop', 'restart']);

if (in_array('restart', $actions->toArray())) {
    // Le restart est autorisé
}
```

---

### ActionStatus

**Fichier:** `src/System/Domain/ValueObject/ActionStatus.php`

Énumération des états d'une ActionRequest.

**États disponibles:**
- `PENDING` - Action en attente de traitement
- `COMPLETED` - Action complétée avec succès
- `FAILED` - Action échouée
- `TIMEOUT` - Action expirée (timeout)

**API:**
```php
$status->isPending(): bool
$status->isSuccess(): bool
$status->isFailure(): bool  // FAILED ou TIMEOUT
```

**Usage:**
```php
use Marvin\System\Domain\ValueObject\ActionStatus;

$status = ActionStatus::PENDING;

if ($status->isPending()) {
    // L'action est en cours
}

if ($status->isFailure()) {
    // L'action a échoué ou timeout
}
```

---

### SupervisorProcess

**Fichier:** `src/System/Domain/ValueObject/SupervisorProcess.php`

Représente le nom d'un process Supervisord.

**Validation:**
- Caractères autorisés: alphanumériques, underscore, tiret
- Regex: `/^[a-z0-9_\-]+$/i`

**Usage:**
```php
use Marvin\System\Domain\ValueObject\SupervisorProcess;

$process = new SupervisorProcess('messenger-consume-async');

echo $process->toString(); // "messenger-consume-async"
```

---

## 3. Application Layer

### Commands & CommandHandlers

#### Container Commands

**Fichiers:**
- `src/System/Application/Command/Container/*.php`
- `src/System/Application/CommandHandler/Container/*Handler.php`

**Commands disponibles:**

1. **StartContainer**
   ```php
   use Marvin\System\Application\Command\Container\StartContainer;
   use Marvin\System\Domain\ValueObject\Identity\ContainerId;
   
   $command = new StartContainer(containerId: $containerId);
   $commandBus->handle($command);
   ```

2. **StopContainer**
   ```php
   use Marvin\System\Application\Command\Container\StopContainer;
   
   $command = new StopContainer(containerId: $containerId);
   $commandBus->handle($command);
   ```

3. **RestartContainer**
   ```php
   use Marvin\System\Application\Command\Container\RestartContainer;
   
   $command = new RestartContainer(containerId: $containerId);
   $commandBus->handle($command);
   ```

4. **BuildContainer**
   ```php
   use Marvin\System\Application\Command\Container\BuildContainer;
   
   $command = new BuildContainer(containerId: $containerId);
   $commandBus->handle($command);
   ```

5. **ExecContainerCommand**
   ```php
   use Marvin\System\Application\Command\Container\ExecContainerCommand;
   
   $command = new ExecContainerCommand(
       containerId: $containerId,
       command: 'ls -la',
   );
   $commandBus->handle($command);
   ```

6. **RestartAllContainer**
   ```php
   use Marvin\System\Application\Command\Container\RestartAllContainer;
   
   $command = new RestartAllContainer();
   $commandBus->handle($command);
   ```

**Pattern des Handlers:**
Les CommandHandlers créent des `ActionRequest` et les envoient au marvin-manager via MQTT pour exécution asynchrone.

---

### Queries & QueryHandlers

#### Container Queries

**1. GetContainer**
```php
use Marvin\System\Application\Query\Container\GetContainer;
use Marvin\System\Domain\ValueObject\Identity\ContainerId;

$query = new GetContainer(id: $containerId);
$container = $queryBus->handle($query);
// Returns: Container|null
```

**2. GetContainerCollection**
```php
use Marvin\System\Application\Query\Container\GetContainerCollection;

$query = new GetContainerCollection(
    type: 'protocol',    // Optionnel: filtrer par type
    status: 'running',   // Optionnel: filtrer par status
);
$containers = $queryBus->handle($query);
// Returns: Container[]
```

#### Worker Queries

**1. GetWorker**
```php
use Marvin\System\Application\Query\Worker\GetWorker;
use Marvin\System\Domain\ValueObject\Identity\WorkerId;

$query = new GetWorker(id: $workerId);
$worker = $queryBus->handle($query);
// Returns: Worker|null
```

**2. GetWorkerCollection**
```php
use Marvin\System\Application\Query\Worker\GetWorkerCollection;

$query = new GetWorkerCollection(
    type: 'consumer',   // Optionnel: filtrer par type
    status: 'running',  // Optionnel: filtrer par status
);
$workers = $queryBus->handle($query);
// Returns: Worker[]
```

#### ActionRequest Queries

**1. GetActionRequest**
```php
use Marvin\System\Application\Query\ActionRequest\GetActionRequest;
use Marvin\System\Domain\ValueObject\Identity\ActionRequestId;

$query = new GetActionRequest(id: $actionRequestId);
$actionRequest = $queryBus->handle($query);
// Returns: ActionRequest|null
```

**2. GetActionRequestByCorrelationId**
```php
use Marvin\System\Application\Query\ActionRequest\GetActionRequestByCorrelationid;
use Marvin\Shared\Domain\ValueObject\Identity\UniqId;

$query = new GetActionRequestByCorrelationid(correlationId: $correlationId);
$actionRequest = $queryBus->handle($query);
// Returns: ActionRequest|null
```

**3. GetTimeoutActionRequestCollection**
```php
use Marvin\System\Application\Query\ActionRequest\GetTimeoutActionRequestCollection;

$query = new GetTimeoutActionRequestCollection(timeoutMinutes: 5);
$actionRequests = $queryBus->handle($query);
// Returns: ActionRequest[]
```

---

### EventHandlers

#### ContainerSyncedHandler

**Fichier:** `src/System/Application/EventHandler/Container/ContainerSyncedHandler.php`

Écoute l'événement `ContainerSynced` et met à jour l'état du conteneur.

**Usage:**
Cet handler est automatiquement appelé quand un événement `ContainerSynced` est dispatché (généralement par le marvin-manager après une synchronisation).

---

#### ContainerActionCompletedHandler

**Fichier:** `src/System/Application/EventHandler/Container/ContainerActionCompletedHandler.php`

Écoute l'événement `ContainerActionCompleted` et met à jour l'`ActionRequest` correspondante.

**Usage:**
Cet handler est automatiquement appelé quand une action sur un conteneur est complétée.

---

## 4. Domain Events

### ContainerSynced

**Fichier:** `src/System/Domain/Event/ContainerSynced.php`

Événement émis quand l'état d'un conteneur est synchronisé.

**Propriétés:**
```php
public UniqId $correlationId;
public ContainerId $containerId;
public ActionStatus $status;
public array $details;
```

**Usage:**
```php
use Marvin\System\Domain\Event\ContainerSynced;
use Marvin\Shared\Domain\ValueObject\Identity\UniqId;

$event = new ContainerSynced(
    correlationId: new UniqId(),
    containerId: $containerId,
    status: ActionStatus::COMPLETED,
    details: ['message' => 'Container synced successfully'],
);

$domainEventBus->dispatch($event);
```

---

### ContainerActionCompleted

**Fichier:** `src/System/Domain/Event/ContainerActionCompleted.php`

Événement émis quand une action sur un conteneur est terminée.

**Propriétés:**
```php
public UniqId $correlationId;
public ContainerId $containerId;
public string $action;
public bool $success;
public ?string $output;
public ?string $error;
```

**Usage:**
```php
use Marvin\System\Domain\Event\ContainerActionCompleted;

$event = new ContainerActionCompleted(
    correlationId: new UniqId(),
    containerId: $containerId,
    action: 'restart',
    success: true,
    output: 'Container restarted successfully',
);

$domainEventBus->dispatch($event);
```

---

## 5. Infrastructure

### Persistence

**Repositories ORM:**
- `src/System/Infrastructure/Persistence/Doctrine/ORM/ContainerOrmRepository.php`
- `src/System/Infrastructure/Persistence/Doctrine/ORM/WorkerOrmRepository.php`
- `src/System/Infrastructure/Persistence/Doctrine/ORM/ActionRequestOrmRepository.php`

**DBAL Types:**
- `src/System/Infrastructure/Persistence/Doctrine/DBAL/Types/ContainerIdType.php`
- `src/System/Infrastructure/Persistence/Doctrine/DBAL/Types/WorkerIdType.php`
- `src/System/Infrastructure/Persistence/Doctrine/DBAL/Types/ActionRequestIdType.php`

**Cache:**
- `src/System/Infrastructure/Persistence/Doctrine/Cache/SystemCacheKeys.php`

**Configuration Doctrine:**
Les mappings XML se trouvent dans `config/doctrine/System/`.

---

## 6. Presentation

### CLI Commands

#### ListContainersCommand

**Fichier:** `src/System/Presentation/Cli/ListContainersCommand.php`

Liste tous les conteneurs avec leurs états.

**Usage:**
```bash
php bin/console marvin:system:list-containers
php bin/console marvin:system:list-containers --type=protocol
php bin/console marvin:system:list-containers --status=running
```

---

#### ListWorkersCommand

**Fichier:** `src/System/Presentation/Cli/ListWorkersCommand.php`

Liste tous les workers Supervisord avec leurs états.

**Usage:**
```bash
php bin/console marvin:system:list-workers
php bin/console marvin:system:list-workers --type=consumer
php bin/console marvin:system:list-workers --status=running
```

---

#### SendContainerCommand

**Fichier:** `src/System/Presentation/Cli/SendContainerCommand.php`

Envoie une action à un conteneur.

**Usage:**
```bash
php bin/console marvin:system:send-container <containerId> <action>
php bin/console marvin:system:send-container abc123 restart
php bin/console marvin:system:send-container abc123 exec "ls -la"
```

---

#### CheckActionRequestTimeoutsCommand

**Fichier:** `src/System/Presentation/Cli/CheckActionRequestTimeoutsCommand.php`

Vérifie et marque comme timeout les ActionRequests expirées.

**Usage:**
```bash
php bin/console marvin:system:check-timeouts
php bin/console marvin:system:check-timeouts --timeout=10
```

**Note:** Cette commande devrait être exécutée périodiquement (cron).

---

## 7. Patterns & Best Practices

### Pattern Async Actions

Le contexte System utilise un pattern d'actions asynchrones pour les opérations sur les conteneurs et workers :

1. **Création de la requête**
   ```php
   $actionRequest = new ActionRequest(
       correlationId: new UniqId(),
       entityType: 'container',
       entityId: $containerId->toString(),
       action: 'restart',
       status: ActionStatus::PENDING,
   );
   $repository->save($actionRequest);
   ```

2. **Envoi via MQTT** (dans le CommandHandler)
   ```php
   $mqttClient->publish(
       topic: 'marvin/manager/action',
       payload: [
           'correlationId' => $actionRequest->correlationId->toString(),
           'entityType' => 'container',
           'entityId' => $containerId->toString(),
           'action' => 'restart',
       ]
   );
   ```

3. **Réception de la réponse** (EventHandler)
   ```php
   // ContainerActionCompletedHandler
   public function __invoke(ContainerActionCompleted $event): void
   {
       $actionRequest = $this->repository->byCorrelationId($event->correlationId);
       $actionRequest->markAsCompleted($event->success, $event->output, $event->error);
       $this->repository->save($actionRequest);
   }
   ```

---

### Gestion des timeouts

Les ActionRequests peuvent expirer si elles ne sont pas complétées dans le délai imparti.

**Détection des timeouts:**
```php
use Marvin\System\Application\Query\ActionRequest\GetTimeoutActionRequestCollection;

$query = new GetTimeoutActionRequestCollection(timeoutMinutes: 5);
$timeoutRequests = $queryBus->handle($query);

foreach ($timeoutRequests as $request) {
    $request->markAsTimeout();
    $repository->save($request);
}
```

**Commande CLI:**
```bash
php bin/console marvin:system:check-timeouts --timeout=5
```

**Mise en place dans cron:**
```bash
*/5 * * * * php /path/to/marvin/bin/console marvin:system:check-timeouts
```

---

### Synchronisation Container/Worker

Les états des conteneurs et workers sont synchronisés périodiquement avec Docker et Supervisord via le `marvin-manager`.

**Flow de synchronisation:**

1. Le `marvin-manager` interroge Docker/Supervisord
2. Il publie les états via MQTT
3. Le `marvin-core` reçoit les événements `ContainerSynced`
4. Les EventHandlers mettent à jour les entités

---

### Querying

**Filtrage par type:**
```php
use Marvin\System\Application\Query\Container\GetContainerCollection;

$query = new GetContainerCollection(type: 'protocol');
$protocolContainers = $queryBus->handle($query);
```

**Filtrage par status:**
```php
$query = new GetContainerCollection(status: 'running');
$runningContainers = $queryBus->handle($query);
```

**Combinaison de filtres:**
```php
$query = new GetContainerCollection(
    type: 'protocol',
    status: 'running'
);
$runningProtocols = $queryBus->handle($query);
```

---

### Gestion des erreurs

**Exceptions du domaine:**
- `ContainerNotFound` - Conteneur introuvable
- `WorkerNotFound` - Worker introuvable
- `ActionRequestNotFound` - ActionRequest introuvable
- `ActionNotAllowed` - Action non autorisée sur l'entité

**Usage:**
```php
use Marvin\System\Domain\Exception\ContainerNotFound;
use Marvin\System\Domain\Exception\ActionNotAllowed;

$container = $repository->byId($containerId);
if (!$container) {
    throw new ContainerNotFound($containerId);
}

if (!$container->isActionAllowed('restart')) {
    throw new ActionNotAllowed('restart', 'container', $containerId);
}
```

---

### Tests

**Créer un Container en test:**
```php
use App\Tests\Factory\System\ContainerFactory;

// Avec Foundry
$container = ContainerFactory::createOne([
    'serviceLabel' => new Label('test-container'),
    'type' => ContainerType::PROTOCOL,
    'status' => ContainerStatus::RUNNING,
]);

// État nommé (si défini dans la Factory)
$zigbeeContainer = ContainerFactory::new()->zigbee()->create();
```

---

## Résumé

Le bounded context **System** offre :

- **3 Domain Models** : Container, Worker, ActionRequest
- **9 Value Objects** : ContainerStatus, ContainerType, ContainerImage, ContainerAllowedActions, WorkerStatus, WorkerType, WorkerAllowedActions, ActionStatus, SupervisorProcess
- **Pattern async** pour les opérations système
- **Synchronisation** avec Docker et Supervisord
- **Gestion des timeouts** pour les actions asynchrones
- **CLI Commands** pour l'administration système

Le contexte System est le pilier de l'infrastructure de Marvin, assurant la disponibilité et le monitoring des services critiques.
