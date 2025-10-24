# MARVIN-MANAGER-BUNDLE - Technical Documentation

## Vue d'ensemble

Le **marvin-manager-bundle** est une bibliothèque qui facilite la communication entre **Marvin Core** et **Marvin Manager** via Symfony Messenger. Il fournit des commandes de messaging standardisées pour envoyer des requêtes d'actions (start, stop, restart, build) sur les containers Docker et workers Supervisor, et recevoir les réponses asynchrones.

## Architecture

### Contexte

Le projet Marvin est composé de 3 applications distinctes :

1. **Marvin Core** - Application principale (Symfony + API Platform + DDD/CQRS)
2. **Marvin Manager** - Gestionnaire de containers Docker et workers Supervisor
3. **Marvin Protocol** - Gestionnaires de protocoles domotiques (Zigbee, Matter, etc.)

Ces applications communiquent via **Symfony Messenger** avec un transport partagé (PostgreSQL).

### Structure du bundle

```
src/
├── MarvinManagerBundle.php
├── Messenger/
│   ├── ManagerRequestCommand.php      # Commande de requête vers Manager
│   └── ManagerResponseCommand.php     # Commande de réponse depuis Manager
└── Reference/
    ├── ManagerContainerActionReference.php   # Actions disponibles pour containers
    └── ManagerWorkerActionReference.php      # Actions disponibles pour workers
```

---

## Core Components

### 1. Action References

#### ManagerContainerActionReference

**Fichier:** `src/Reference/ManagerContainerActionReference.php`

Enum définissant les actions disponibles pour les containers Docker.

```php
enum ManagerContainerActionReference: string
{
    use EnumToArrayTrait;

    case ACTION_START = 'start';
    case ACTION_RESTART = 'restart';
    case ACTION_RESTART_ALL = 'restart_all';
    case ACTION_STOP = 'stop';
    case ACTION_BUILD = 'build';
    case ACTION_EXEC_CMD = 'exec_cmd';
}
```

**Actions disponibles:**

| Action | Description | Usage |
|--------|-------------|-------|
| `start` | Démarre un container | `docker compose start <service>` |
| `restart` | Redémarre un container | `docker compose restart <service>` |
| `restart_all` | Redémarre tous les containers | `docker compose restart` |
| `stop` | Arrête un container | `docker compose stop <service>` |
| `build` | Rebuild un container | `docker compose build <service>` |
| `exec_cmd` | Execute une commande dans un container | `docker compose exec <service> <cmd>` |

**Méthodes utilitaires (via EnumToArrayTrait):**
```php
// Récupérer toutes les valeurs
$actions = ManagerContainerActionReference::values();
// ['start', 'restart', 'restart_all', 'stop', 'build', 'exec_cmd']

// Récupérer tous les cases
$cases = ManagerContainerActionReference::cases();
```

---

#### ManagerWorkerActionReference

**Fichier:** `src/Reference/ManagerWorkerActionReference.php`

Enum définissant les actions disponibles pour les workers Supervisor.

```php
enum ManagerWorkerActionReference: string
{
    use EnumToArrayTrait;

    case ACTION_START = 'start';
    case ACTION_STOP = 'stop';
    case ACTION_RESTART = 'restart';
    case ACTION_REREAD = 'reread';
    case ACTION_UPDATE = 'update';
}
```

**Actions disponibles:**

| Action | Description | Usage |
|--------|-------------|-------|
| `start` | Démarre un worker | `supervisorctl start <worker>` |
| `stop` | Arrête un worker | `supervisorctl stop <worker>` |
| `restart` | Redémarre un worker | `supervisorctl restart <worker>` |
| `reread` | Relit la configuration Supervisor | `supervisorctl reread` |
| `update` | Met à jour la configuration et démarre nouveaux workers | `supervisorctl update` |

---

### 2. Messenger Commands

#### ManagerRequestCommand

**Fichier:** `src/Messenger/ManagerRequestCommand.php`

Commande asynchrone pour envoyer une requête d'action à Marvin Manager.

```php
final readonly class ManagerRequestCommand implements CommandInterface
{
    public function __construct(
        public string $containerId,      // ID du container ou worker
        public string $correlationId,    // UUID pour tracer la requête
        public string $action,           // Action à exécuter
        public ?string $command = null,  // Commande optionnelle (pour exec_cmd)
        public array $args = [],         // Arguments optionnels
        public int $timeout = 10,        // Timeout en secondes
    ) {}
}
```

**Propriétés:**

- **`containerId`** (string, required)
  - ID de l'entité (container ou worker) sur laquelle exécuter l'action
  - Format UUID pour containers/workers Marvin

- **`correlationId`** (string, required)
  - UUID unique pour corréler la requête avec la réponse
  - Généré par Marvin Core, utilisé pour tracer l'action

- **`action`** (string, required)
  - Action à exécuter (voir `ManagerContainerActionReference` ou `ManagerWorkerActionReference`)
  - Validé avec `#[Choice]` pour accepter uniquement les actions valides

- **`command`** (string, optional)
  - Commande à exécuter (uniquement pour `exec_cmd`)
  - Exemple: `"php bin/console cache:clear"`

- **`args`** (array, optional, default: `[]`)
  - Arguments additionnels pour certaines actions
  - Format clé-valeur

- **`timeout`** (int, optional, default: `10`)
  - Timeout maximum pour l'exécution de l'action en secondes

**Validation:**
- `#[NotBlank]` sur les champs requis
- `#[Choice]` sur `action` pour garantir une action valide

**Exemple d'utilisation:**
```php
use EnderLab\MarvinManagerBundle\Messenger\ManagerRequestCommand;
use EnderLab\MarvinManagerBundle\Reference\ManagerContainerActionReference;
use Marvin\Shared\Domain\ValueObject\Identity\UniqId;

// Démarrer un container
$command = new ManagerRequestCommand(
    containerId: $container->id->toString(),
    correlationId: UniqId::generate()->toString(),
    action: ManagerContainerActionReference::ACTION_START->value
);

$this->commandBus->dispatch($command);
```

---

#### ManagerResponseCommand

**Fichier:** `src/Messenger/ManagerResponseCommand.php`

Commande asynchrone retournée par Marvin Manager avec le résultat de l'action.

```php
final readonly class ManagerResponseCommand implements CommandInterface
{
    public function __construct(
        public string $correlationId,    // UUID de corrélation
        public string $entityType,       // 'container' ou 'worker'
        public string $entityId,         // ID de l'entité
        public string $action,           // Action exécutée
        public bool $success = false,    // Succès ou échec
        public ?string $output = null,   // Output de la commande
        public ?string $error = null,    // Message d'erreur si échec
        public ?array $metadata = [],    // Métadonnées additionnelles
    ) {}

    public function isSuccess(): bool
    public function isFailed(): bool
    public function hasError(): bool
}
```

**Propriétés:**

- **`correlationId`** (string, required)
  - UUID de corrélation pour relier à la requête initiale
  - Même valeur que dans `ManagerRequestCommand`

- **`entityType`** (string, required)
  - Type d'entité: `"container"` ou `"worker"`
  - Validé avec `#[Choice]`

- **`entityId`** (string, required)
  - ID de l'entité (même que `containerId` dans la requête)

- **`action`** (string, required)
  - Action exécutée (même que dans la requête)

- **`success`** (bool, default: `false`)
  - `true` si l'action a réussi, `false` sinon

- **`output`** (string, optional)
  - Output de la commande exécutée
  - Exemple: logs, résultat de `docker compose ps`, etc.

- **`error`** (string, optional)
  - Message d'erreur en cas d'échec
  - Contient les détails de l'erreur pour debugging

- **`metadata`** (array, optional, default: `[]`)
  - Données additionnelles (status, uptime, etc.)
  - Format clé-valeur

**Méthodes helpers:**
```php
if ($response->isSuccess()) {
    // Action réussie
}

if ($response->isFailed()) {
    // Action échouée
}

if ($response->hasError()) {
    // Erreur disponible dans $response->error
}
```

**Exemple d'utilisation:**
```php
#[AsMessageHandler]
final readonly class ManagerResponseHandler
{
    public function __invoke(ManagerResponseCommand $response): void
    {
        // Récupérer l'ActionRequest via correlationId
        $actionRequest = $this->actionRequestRepository
            ->byCorrelationId(new UniqId($response->correlationId));

        if ($response->isSuccess()) {
            $actionRequest->markAsCompleted(true, $response->output);
        } else {
            $actionRequest->markAsCompleted(false, null, $response->error);
        }

        $this->actionRequestRepository->save($actionRequest);
    }
}
```

---

## Patterns et Workflows

### 1. Workflow Complet: Start Container

#### Étape 1: Marvin Core envoie la requête

```php
// Dans Marvin Core - BuildContainerHandler
final readonly class BuildContainerHandler
{
    public function __invoke(BuildContainer $command): void
    {
        // 1. Vérifier que l'action est autorisée
        $container = $this->containerRepository->byId($command->containerId);
        if (!$container->isActionAllowed('build')) {
            throw ActionNotAllowed::withContainerAndAction(...);
        }

        // 2. Créer un ActionRequest (tracking)
        $actionRequest = new ActionRequest(
            $command->correlationId,
            'container',
            $command->containerId->toString(),
            'build',
            ActionStatus::PENDING
        );
        $this->actionRequestRepository->save($actionRequest);

        // 3. Dispatcher la requête vers Manager
        $managerMessage = new ManagerRequestCommand(
            $command->containerId->toString(),
            $command->correlationId->toString(),
            ManagerContainerActionReference::ACTION_BUILD->value
        );
        $this->commandBus->dispatch($managerMessage);
    }
}
```

#### Étape 2: Symfony Messenger route la commande

**Configuration Messenger:**
```yaml
# config/packages/messenger.yaml (Marvin Core)
framework:
    messenger:
        transports:
            postgres: 'doctrine://default'
        
        routing:
            'EnderLab\MarvinManagerBundle\Messenger\ManagerRequestCommand': postgres
            'EnderLab\MarvinManagerBundle\Messenger\ManagerResponseCommand': postgres
```

La commande est persistée dans PostgreSQL et consommée par Marvin Manager.

#### Étape 3: Marvin Manager consomme et exécute

```php
// Dans Marvin Manager - ManagerRequestHandler
#[AsMessageHandler]
final readonly class ManagerRequestHandler
{
    public function __invoke(ManagerRequestCommand $request): void
    {
        try {
            // Exécuter l'action Docker
            $output = $this->dockerService->build($request->containerId);
            
            // Envoyer la réponse
            $response = new ManagerResponseCommand(
                correlationId: $request->correlationId,
                entityType: 'container',
                entityId: $request->containerId,
                action: $request->action,
                success: true,
                output: $output
            );
            
            $this->commandBus->dispatch($response);
        } catch (\Exception $e) {
            // Envoyer réponse d'erreur
            $response = new ManagerResponseCommand(
                correlationId: $request->correlationId,
                entityType: 'container',
                entityId: $request->containerId,
                action: $request->action,
                success: false,
                error: $e->getMessage()
            );
            
            $this->commandBus->dispatch($response);
        }
    }
}
```

#### Étape 4: Marvin Core consomme la réponse

```php
// Dans Marvin Core - ManagerResponseHandler
#[AsMessageHandler]
final readonly class ManagerResponseHandler
{
    public function __invoke(ManagerResponseCommand $response): void
    {
        // Récupérer l'ActionRequest
        $actionRequest = $this->actionRequestRepository
            ->byCorrelationId(new UniqId($response->correlationId));

        // Mettre à jour le status
        $actionRequest->markAsCompleted(
            $response->success,
            $response->output,
            $response->error
        );

        $this->actionRequestRepository->save($actionRequest);

        // Dispatcher un événement de domaine
        if ($response->success) {
            $event = new ContainerActionCompleted(
                new UniqId($response->correlationId),
                new ContainerId($response->entityId),
                $response->action,
                true,
                $response->output
            );
            $this->eventBus->dispatch($event);
        }
    }
}
```

---

### 2. Workflow: Execute Command in Container

```php
// Exécuter une commande dans un container
$command = new ManagerRequestCommand(
    containerId: $container->id->toString(),
    correlationId: UniqId::generate()->toString(),
    action: ManagerContainerActionReference::ACTION_EXEC_CMD->value,
    command: 'php bin/console cache:clear',
    timeout: 30
);

$this->commandBus->dispatch($command);
```

**Réponse attendue:**
```php
ManagerResponseCommand {
    correlationId: "...",
    entityType: "container",
    entityId: "...",
    action: "exec_cmd",
    success: true,
    output: "Cache cleared successfully",
    error: null,
    metadata: []
}
```

---

### 3. Workflow: Restart Worker

```php
// Redémarrer un worker Supervisor
$command = new ManagerRequestCommand(
    containerId: $worker->id->toString(),
    correlationId: UniqId::generate()->toString(),
    action: ManagerWorkerActionReference::ACTION_RESTART->value
);

$this->commandBus->dispatch($command);
```

---

## Gestion des Erreurs

### Types d'erreurs possibles

1. **Timeout**
   - L'action dépasse le timeout configuré
   - `ActionRequest::markAsTimeout()` dans Marvin Core

2. **Commande invalide**
   - Action non supportée ou mal formée
   - Validation Symfony échoue avant l'envoi

3. **Erreur d'exécution**
   - Docker/Supervisor renvoie une erreur
   - `ManagerResponseCommand::success = false` avec `error` rempli

4. **Entité introuvable**
   - Container ou worker n'existe pas
   - Erreur retournée dans `ManagerResponseCommand`

### Exemple de gestion d'erreur

```php
#[AsMessageHandler]
final readonly class ManagerResponseHandler
{
    public function __invoke(ManagerResponseCommand $response): void
    {
        $actionRequest = $this->actionRequestRepository
            ->byCorrelationId(new UniqId($response->correlationId));

        if ($response->isFailed()) {
            // Logger l'erreur
            $this->logger->error('Manager action failed', [
                'correlation_id' => $response->correlationId,
                'action' => $response->action,
                'error' => $response->error,
            ]);

            // Marquer comme échoué
            $actionRequest->markAsCompleted(false, null, $response->error);

            // Notifier l'utilisateur
            $this->notificationService->send(
                'Action échouée',
                $response->error
            );
        } else {
            $actionRequest->markAsCompleted(true, $response->output);
        }

        $this->actionRequestRepository->save($actionRequest);
    }
}
```

---

## Configuration

### 1. Messenger Routing (Marvin Core)

```yaml
# config/packages/messenger.yaml
framework:
    messenger:
        transports:
            # Transport partagé avec Manager
            postgres:
                dsn: '%env(MESSENGER_TRANSPORT_DSN)%'
                options:
                    queue_name: manager_messages
        
        routing:
            # Requêtes vers Manager
            'EnderLab\MarvinManagerBundle\Messenger\ManagerRequestCommand': postgres
            
            # Réponses depuis Manager
            'EnderLab\MarvinManagerBundle\Messenger\ManagerResponseCommand': postgres
```

### 2. Messenger Routing (Marvin Manager)

```yaml
# config/packages/messenger.yaml
framework:
    messenger:
        transports:
            postgres:
                dsn: '%env(MESSENGER_TRANSPORT_DSN)%'
                options:
                    queue_name: manager_messages
        
        routing:
            'EnderLab\MarvinManagerBundle\Messenger\ManagerRequestCommand': postgres
            'EnderLab\MarvinManagerBundle\Messenger\ManagerResponseCommand': postgres
```

### 3. Consumer Worker

**Marvin Core:**
```bash
# Consommer les réponses de Manager
php bin/console messenger:consume postgres -vv
```

**Marvin Manager:**
```bash
# Consommer les requêtes de Core
php bin/console messenger:consume postgres -vv
```

**Supervisord (production):**
```ini
[program:marvin_core_messenger]
command=php /app/bin/console messenger:consume postgres --time-limit=3600
directory=/app
autostart=true
autorestart=true
numprocs=2

[program:marvin_manager_messenger]
command=php /app/bin/console messenger:consume postgres --time-limit=3600
directory=/app
autostart=true
autorestart=true
numprocs=1
```

---

## Patterns et Best Practices

### 1. Correlation ID

**Toujours générer un UUID unique:**
```php
use Marvin\Shared\Domain\ValueObject\Identity\UniqId;

$correlationId = UniqId::generate();
```

**Utiliser le même ID pour tracer la requête:**
- Enregistrement dans `ActionRequest`
- Log des événements
- Corrélation avec la réponse

### 2. ActionRequest Tracking

**Créer un ActionRequest avant chaque requête:**
```php
$actionRequest = new ActionRequest(
    $correlationId,
    'container',  // ou 'worker'
    $entityId,
    $action,
    ActionStatus::PENDING
);
$this->actionRequestRepository->save($actionRequest);
```

**Permet:**
- Tracking de l'état de l'action
- Timeout detection
- Historique des actions
- Retry logic si nécessaire

### 3. Timeout Handling

**Définir un timeout approprié selon l'action:**
```php
$timeout = match($action) {
    'build' => 300,      // 5 minutes pour un build
    'start' => 30,       // 30 secondes pour un start
    'exec_cmd' => 60,    // 1 minute pour exec
    default => 10
};

$command = new ManagerRequestCommand(
    ...,
    timeout: $timeout
);
```

**Vérifier les timeouts côté Core:**
```php
// Commande CLI périodique
php bin/console marvin:system:check-action-timeouts

// Handler
final class CheckActionRequestTimeoutsHandler
{
    public function execute(): void
    {
        $pendingRequests = $this->actionRequestRepository
            ->findPendingOlderThan(new \DateInterval('PT30S'));

        foreach ($pendingRequests as $request) {
            $request->markAsTimeout();
            $this->actionRequestRepository->save($request);
        }
    }
}
```

### 4. Idempotence

**Les actions doivent être idempotentes:**
- Envoyer 2x `start` sur un container déjà démarré ne doit pas échouer
- Utiliser `docker compose start` (idempotent) plutôt que `up` (peut recréer)

### 5. Événements de Domaine

**Dispatcher des événements après succès:**
```php
if ($response->isSuccess()) {
    $event = match($response->entityType) {
        'container' => new ContainerActionCompleted(...),
        'worker' => new WorkerActionCompleted(...),
    };
    
    $this->eventBus->dispatch($event);
}
```

**Permet:**
- Notification des utilisateurs
- Logs centralisés
- Réactions inter-bounded-contexts

---

## Avantages du Bundle

### 1. Communication Standardisée
- API uniforme pour toutes les actions containers/workers
- Validation automatique des actions
- Type-safe avec enums PHP 8.4

### 2. Asynchronisme
- Actions longues (build, restart) non bloquantes
- Scalabilité via workers Messenger
- Retry automatique en cas d'échec

### 3. Traçabilité
- Correlation ID pour suivre chaque action
- ActionRequest pour historique
- Logs structurés

### 4. Découplage
- Marvin Core ne connaît pas Docker/Supervisor
- Marvin Manager isolé, remplaçable
- Communication via contrat (commands)

### 5. Résilience
- Timeout configurable
- Retry policy Messenger
- Gestion d'erreurs explicite

---

## Exemples d'Utilisation

### Exemple 1: Restart All Containers

```php
// Command Handler dans Marvin Core
final readonly class RestartAllContainersHandler
{
    public function __invoke(RestartAllContainers $command): void
    {
        $correlationId = UniqId::generate();
        
        // Créer ActionRequest
        $actionRequest = new ActionRequest(
            $correlationId,
            'container',
            'all',
            ManagerContainerActionReference::ACTION_RESTART_ALL->value,
            ActionStatus::PENDING
        );
        $this->actionRequestRepository->save($actionRequest);
        
        // Envoyer requête
        $request = new ManagerRequestCommand(
            containerId: 'all',
            correlationId: $correlationId->toString(),
            action: ManagerContainerActionReference::ACTION_RESTART_ALL->value,
            timeout: 120
        );
        
        $this->commandBus->dispatch($request);
    }
}
```

### Exemple 2: Update Supervisor Configuration

```php
// Recharger la configuration Supervisor après modification
$command = new ManagerRequestCommand(
    containerId: 'supervisor',
    correlationId: UniqId::generate()->toString(),
    action: ManagerWorkerActionReference::ACTION_REREAD->value
);
$this->commandBus->dispatch($command);

// Puis appliquer les changements
$command = new ManagerRequestCommand(
    containerId: 'supervisor',
    correlationId: UniqId::generate()->toString(),
    action: ManagerWorkerActionReference::ACTION_UPDATE->value
);
$this->commandBus->dispatch($command);
```

---

## Dépendances

- PHP 8.4+
- Symfony 7.3+
- Symfony Messenger
- enderlab/ddd-cqrs-bundle
- enderlab/tools-bundle

---

## Ressources

### Documentation externe
- [Symfony Messenger](https://symfony.com/doc/current/messenger.html)
- [Docker Compose CLI](https://docs.docker.com/compose/reference/)
- [Supervisor](http://supervisord.org/)

---

**Dernière mise à jour:** 2025-10-24
**Auteur:** Documentation technique générée pour l'équipe de développement Marvin
