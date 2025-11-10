# Device Context

**Bounded Context** pour la gestion du cycle de vie des devices domotiques (physiques, virtuels et composites).

## 📋 Vue d'ensemble

Le contexte Device gère les devices domotiques de bout en bout : création, configuration, états, capabilities, actions, groupes et scènes. Il orchestre les commandes et délègue l'exécution au Protocol Context.

### Responsabilités

✅ **Ce que Device fait** :
- Gestion du cycle de vie des devices (CRUD)
- Gestion des capabilities (switch, brightness, temperature, etc.)
- Gestion des états (state tracking)
- Exécution d'actions (turn_on, set_brightness, etc.)
- Gestion des groupes (composite devices)
- Gestion des scènes
- Orchestration des commandes avec modes d'exécution
- Gestion du système PendingAction

❌ **Ce que Device NE fait PAS** :
- Communication protocol bas niveau (→ Protocol Context)
- Stockage des credentials (→ Secret Context)
- Logique d'automation (→ Automation Context)

---

## 🏗️ Architecture

### Structure DDD

```
src/Device/
├── Domain/                      # Logique métier
│   ├── Model/
│   │   ├── Device.php           # Aggregate Root
│   │   ├── DeviceCapability.php # Entity
│   │   └── PendingAction.php    # Aggregate Root
│   │
│   ├── ValueObject/
│   │   ├── DeviceType.php          (ACTUATOR, SENSOR, COMPOSITE, VIRTUAL)
│   │   ├── DeviceStatus.php        (ONLINE, OFFLINE, UNKNOWN, PAIRING)
│   │   ├── CompositeType.php       (GROUP, SCENE)
│   │   ├── CompositeStrategy.php   (MARVIN_MANAGED, NATIVE_PROTOCOL, HYBRID)
│   │   ├── Capability.php          (enum ~50 capabilities)
│   │   ├── CapabilityAction.php    (enum ~150 actions)
│   │   ├── CapabilityState.php     (enum ~100 states avec contraintes)
│   │   ├── CapabilityStateDataType.php (BOOLEAN, INTEGER, FLOAT, STRING, ENUM, ARRAY, OBJECT)
│   │   ├── ExecutionStrategy.php   (BROADCAST, SEQUENTIAL, PRIORITY)
│   │   ├── Protocol.php            (ZIGBEE, MQTT, REST, JSONRPC, WEBSOCKET, BLUETOOTH, NETWORK)
│   │   ├── PhysicalAddress.php
│   │   ├── TechnicalName.php
│   │   ├── NativeGroupInfo.php
│   │   ├── NativeSceneInfo.php
│   │   ├── SceneStates.php
│   │   ├── VirtualDeviceType.php   (TIME, WEATHER, HTTP)
│   │   ├── VirtualDeviceConfig.php
│   │   └── PendingActionStatus.php (WAITING, COMPLETED, FAILED, TIMEOUT)
│   │
│   ├── Event/
│   │   ├── Device/
│   │   │   ├── DeviceCreated.php
│   │   │   ├── DeviceStateChanged.php
│   │   │   ├── DeviceActionExecuted.php
│   │   │   └── ...
│   │   └── PendingAction/
│   │       ├── PendingActionCreated.php
│   │       ├── PendingActionCompleted.php
│   │       ├── PendingActionFailed.php
│   │       └── PendingActionTimeout.php
│   │
│   ├── Repository/
│   │   ├── DeviceRepositoryInterface.php
│   │   ├── DeviceCapabilityRepositoryInterface.php
│   │   └── PendingActionRepositoryInterface.php
│   │
│   └── Exception/
│       ├── DeviceNotFoundException.php
│       ├── CapabilityNotSupportedException.php
│       └── ... (30+ exceptions)
│
├── Application/                 # Use cases
│   ├── Command/
│   │   ├── Device/
│   │   │   ├── CreatePhysicalDevice.php
│   │   │   ├── CreateVirtualDevice.php
│   │   │   ├── ExecuteDeviceAction.php
│   │   │   ├── UpdateDeviceState.php
│   │   │   └── DeleteDevice.php
│   │   └── PendingAction/
│   │       ├── CompletePendingAction.php
│   │       └── FailPendingAction.php
│   │
│   ├── CommandHandler/
│   ├── Query/
│   ├── QueryHandler/
│   ├── EventHandler/
│   │   └── Shared/
│   │       └── DeviceStateChangedHandler.php
│   │
│   └── Service/
│       └── Acl/
│           └── ProtocolCapabilityServiceInterface.php
│
├── Infrastructure/
│   ├── Persistence/
│   │   └── Doctrine/
│   │       ├── ORM/
│   │       │   ├── Repository/
│   │       │   │   ├── DoctrineDeviceRepository.php
│   │       │   │   └── DoctrinePendingActionRepository.php
│   │       │   └── Mapping/
│   │       │       ├── Model.Device.orm.xml
│   │       │       ├── Model.DeviceCapability.orm.xml
│   │       │       ├── Model.PendingAction.orm.xml
│   │       │       ├── ValueObject.NativeGroupInfo.orm.xml
│   │       │       ├── ValueObject.NativeSceneInfo.orm.xml
│   │       │       ├── ValueObject.PhysicalAddress.orm.xml
│   │       │       ├── ValueObject.SceneStates.orm.xml
│   │       │       ├── ValueObject.TechnicalName.orm.xml
│   │       │       └── ValueObject.VirtualDeviceConfig.orm.xml
│   │       └── DBAL/
│   │           └── Types/
│   │               └── DeviceCapabilityIdType.php
│   │
│   └── Framework/
│       └── Symfony/
│           └── Service/
│               └── Acl/
│                   └── ProtocolCapabilityService.php
│
└── Presentation/
    └── Cli/
```

---

## 🎯 Modèles principaux

### Device (Aggregate Root)

Représente un device domotique (physique, virtuel ou composite).

**Propriétés principales** :
```php
class Device extends AggregateRoot
{
    private DeviceId $id;
    private Label $label;
    private ?Description $description;
    private DeviceType $deviceType;              // ACTUATOR, SENSOR, COMPOSITE, VIRTUAL
    private DeviceStatus $status;                // ONLINE, OFFLINE, UNKNOWN, PAIRING

    // Physical device properties
    private ?Protocol $protocol;                 // ZIGBEE, MQTT, REST, JSONRPC, WEBSOCKET, BLUETOOTH
    private ?ProtocolId $protocolId;
    private ?PhysicalAddress $physicalAddress;   // Friendly name, MAC, IP, etc.
    private ?TechnicalName $technicalName;       // Nom technique unique

    // Composite device properties
    private ?CompositeType $compositeType;       // GROUP, SCENE
    private ?CompositeStrategy $compositeStrategy;  // MARVIN_MANAGED, NATIVE_PROTOCOL, HYBRID
    private ?ExecutionStrategy $executionStrategy;  // BROADCAST, SEQUENTIAL, PRIORITY
    private array $childDeviceIds;               // DeviceId[]
    private ?NativeGroupInfo $nativeGroupInfo;   // Infos groupe natif protocol
    private array $nativeSubGroups;
    private ?NativeSceneInfo $nativeSceneInfo;   // Infos scène native protocol
    private ?SceneStates $sceneStates;           // États stockés pour scène

    // Virtual device properties
    private ?VirtualDeviceType $virtualType;     // TIME, WEATHER, HTTP
    private ?VirtualDeviceConfig $virtualConfig;

    // Common properties
    private ?ZoneId $zoneId;
    private Metadata $metadata;
    private DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $lastSeenAt;
    private ?DateTimeImmutable $lastStateUpdateAt;

    // Capabilities relationship
    public private(set) array $capabilities = [];  // DeviceCapability[]
}
```

**Méthodes principales** :
```php
// Création
public static function createPhysical(...): self
public static function createVirtual(...): self
public static function createComposite(...): self   // Pour GROUP et SCENE

// Capabilities management
public function addCapability(Capability $capability, mixed $initialValue = null): void
public function addCapabilityWithState(Capability $capability, string $stateName, mixed $initialValue = null): void
public function hasCapability(Capability $capability): bool
public function getCurrentState(): array

// State management
public function updateState(array $newState): void
public function updatePartialState(string $stateName, mixed $value, ?string $unit = null): void
public function markOnline(): void
public function markOffline(): void

// Composites
public function removeChildDevice(DeviceId $childDeviceId): void
public function updateSceneStates(SceneStates $newStates): void

// Type checks
public function isPhysical(): bool
public function isComposite(): bool
public function isVirtual(): bool
public function isReadOnly(): bool
```

---

### DeviceCapability (Entity)

Représente une capability d'un device avec son état actuel.

**Propriétés** :
```php
class DeviceCapability
{
    private DeviceCapabilityId $id;
    public private(set) ?Device $device = null;
    private Capability $capability;              // Enum de la capability
    private string $stateName;                   // Nom du state (ex: "brightness", "is_heating")
    private mixed $currentValue;                 // Valeur actuelle (type mixte)
    private ?DateTimeImmutable $lastUpdatedAt;   // Date dernière MAJ
    private ?Metadata $metadata = null;          // Métadonnées (unit, etc.)
}
```

**Méthodes** :
```php
public static function create(
    Capability $capability,
    ?string $stateName = null,
    mixed $initialValue = null,
    array $metadata = [],
): self

public function updateValue(mixed $newValue): void
public function setUnit(string $unit): void
public function isReadOnly(): bool
public function toStateArray(): array
```

**Capabilities disponibles** (enum `Capability`) :

**LIGHTING** (10 capabilities) :
- `SWITCH` - Interrupteur marche/arrêt
- `BRIGHTNESS` - Luminosité (0-100%)
- `COLOR_TEMPERATURE` - Température couleur (Kelvin/mireds)
- `COLOR_RGB` - Couleur RGB
- `COLOR_HSV` - Couleur HSV
- `LIGHT_EFFECT` - Effet lumineux
- `DIMMER` - Gradateur
- `WHITE_TEMPERATURE` - Température blanc
- `COLOR_MODE` - Mode couleur
- `TRANSITION` - Durée transition

**CLIMATE** (15 capabilities) :
- `TEMPERATURE` - Température ambiante
- `HUMIDITY` - Humidité
- `PRESSURE` - Pression atmosphérique
- `TARGET_TEMPERATURE` - Température cible
- `HEATING_SETPOINT` - Consigne chauffage
- `COOLING_SETPOINT` - Consigne climatisation
- `THERMOSTAT_MODE` - Mode thermostat
- `FAN_MODE` - Mode ventilateur
- `FAN_SPEED` - Vitesse ventilateur
- `AIR_QUALITY` - Qualité de l'air
- ... (et plus)

**SECURITY** (10 capabilities) :
- `MOTION` - Détection mouvement
- `CONTACT` - Contact (porte/fenêtre)
- `OCCUPANCY` - Présence
- `VIBRATION` - Détection vibration
- `SMOKE` - Détecteur fumée
- `WATER_LEAK` - Fuite d'eau
- `LOCK` - Serrure
- `ALARM` - Alarme
- `TAMPER` - Anti-sabotage
- `GLASS_BREAK` - Bris de vitre

**ENERGY** (5 capabilities) :
- `POWER` - Puissance (Watts)
- `ENERGY` - Énergie consommée (kWh)
- `VOLTAGE` - Tension (Volts)
- `CURRENT` - Courant (Ampères)
- `POWER_FACTOR` - Facteur de puissance

**INFORMATION** (10 capabilities) :
- `BATTERY` - Niveau batterie
- `BATTERY_LOW` - Batterie faible
- `LINKQUALITY` - Qualité signal
- `RSSI` - Force du signal
- `FIRMWARE_VERSION` - Version firmware
- `LAST_SEEN` - Dernière activité
- `UPDATE_AVAILABLE` - MAJ disponible
- `DEVICE_TEMPERATURE` - Température interne
- ... (et plus)

Total : **~50 capabilities** couvrant tous les types de devices domotiques

---

### PendingAction (Aggregate Root)

Track les actions device asynchrones en attente de completion.

**Propriétés** :
```php
class PendingAction extends AggregateRoot
{
    private PendingActionId $id;
    private DeviceId $deviceId;
    private ?CorrelationId $correlationId;
    private PendingActionStatus $status;        // WAITING, COMPLETED, FAILED, TIMEOUT
    private string $capability;
    private string $action;
    private array $parameters;
    private ?array $result;
    private ?string $errorMessage;
    private DateTimeInterface $createdAt;
    private ?DateTimeInterface $completedAt;
    private int $timeoutSeconds;
}
```

**Méthodes** :
```php
// Création
public static function createWithCorrelation(...): self  // CORRELATION_ID mode
public static function createWithDeviceLock(...): self   // DEVICE_LOCK mode

// Lifecycle
public function complete(array $result): void
public function fail(string $errorMessage): void
public function timeout(): void
public function hasExpired(): bool
```

**Lifecycle** :
```
1. Created (WAITING)
   ↓
2a. Complete() → COMPLETED (with result)
2b. Fail() → FAILED (with error)
2c. Timeout() → TIMEOUT (after expiry)
```

---

## ⚙️ Modes d'exécution (ProtocolCapabilityService)

Le `ProtocolCapabilityService` (ACL vers Protocol Context) gère 3 modes d'exécution :

### 1. FIRE_AND_FORGET

**Timeout** : `0` (immédiat)

```php
$this->protocolCapability->executeAction(
    protocolId: $device->protocolId->toString(),
    nativeId: $device->physicalAddress->value,
    capability: 'switch',
    action: 'turn_on',
    parameters: [],
    timeout: 0  // Fire and forget
);
// Returns immediately (void)
```

---

### 2. CORRELATION_ID

**Timeout** : `5000ms` (5 secondes par défaut)

```php
$result = $this->protocolCapability->executeAction(
    protocolId: $device->protocolId->toString(),
    nativeId: $device->physicalAddress->value,
    capability: 'switch',
    action: 'turn_on',
    parameters: [],
    timeout: 5000  // Wait 5 seconds
);
// Returns: ['success' => true, 'result' => [...]]
```

**Flow interne** :
1. Génère `correlationId`
2. Crée `PendingAction(correlationId, WAITING)`
3. Envoie commande via Protocol avec correlation
4. Polling `PendingAction` toutes les 100ms
5. Si `COMPLETED` → Return result
6. Si `TIMEOUT` → Throw `DeviceTimeoutException`

---

### 3. DEVICE_LOCK (Fallback automatique)

**Timeout** : `5000ms`

Si `CORRELATION_ID` échoue (protocol ne supporte pas), fallback automatique vers `DEVICE_LOCK` :

```php
try {
    return $this->executeWithCorrelation(...);
} catch (\Throwable $e) {
    return $this->executeWithDeviceLock(...);  // Automatic fallback
}
```

**Flow interne** :
1. Vérifie si device déjà locked
2. Crée `PendingAction(deviceId, WAITING)` → LOCK
3. Envoie commande via Protocol
4. Polling `PendingAction` toutes les 100ms
5. `DeviceStateChangedHandler` complète le PendingAction
6. Return result

---

## 🔄 Intégration Protocol ↔ Device

### Device → Protocol (executeAction)

```
ExecuteDeviceActionHandler (Device Context)
  ↓
ProtocolCapabilityService (ACL)
  ↓
  1. Choix mode exécution (CORRELATION_ID ou DEVICE_LOCK)
  2. Création PendingAction
  3. Appel Protocol Adapter
  4. Polling PendingAction
  5. Return result
  ↓
Protocol Adapter (Protocol Context)
  ↓
MQTT/REST/WebSocket
```

---

### Protocol → Device (state updates)

```
MQTT Message received
  ↓
MqttDeviceStateListener (Protocol Context)
  ↓
Adapter.transformMessage() → DeviceStateChanged Event
  ↓
EventBus.dispatch()
  ↓
DeviceStateChangedHandler (Device Context)
  ↓
Device.updatePartialState()
  ↓
Device.markOnline()
  ↓
DeviceRepository.save()
```

---

### Protocol → Device (response handling)

```
MQTT Response received (topic: marvin/response/{correlationId})
  ↓
MqttDeviceResponseListener (Protocol Context)
  ↓
Extract correlationId from topic
  ↓
Dispatch CompletePendingAction or FailPendingAction command
  ↓
CompletePendingActionHandler (Device Context)
  ↓
PendingAction.complete(result)
  ↓
PendingActionRepository.save()
  ↓
Polling détecte completion → Return result
```

---

## 📊 Types de Devices

### Physical Device

Device matériel connecté via un protocol.

**Exemples** :
- Ampoule Philips Hue (Zigbee)
- Interrupteur Sonoff (Tasmota/MQTT)
- Prise Shelly (REST ou MQTT)
- Capteur température Aqara (Zigbee)

**Création** :
```php
$device = Device::createPhysical(
    label: new Label('Salon - Lampe principale'),
    protocol: Protocol::ZIGBEE,
    physicalAddress: PhysicalAddress::fromString('living_room_light'),
    protocolId: new ProtocolId('protocol-zigbee-001'),
    manufacturer: new Manufacturer('Philips'),
    model: new Model('Hue White and Color'),
    firmwareVersion: new FirmwareVersion('1.88.1'),
);
```

---

### Virtual Device

Device virtuel basé sur des données externes (API, temps, météo).

**Types virtuels** :
- `TIME` - Lever/coucher du soleil
- `WEATHER` - Données météo (via OpenWeatherMap)
- `HTTP` - API REST externe

**Exemples** :
- Virtual Sun (sunrise/sunset triggers)
- Virtual Weather (temperature, humidity externe)
- Virtual API sensor (stock price, etc.)

**Création** :
```php
$device = Device::createVirtual(
    label: new Label('Météo Extérieure'),
    virtualType: VirtualDeviceType::WEATHER,
    config: VirtualDeviceConfig::fromArray([
        'provider' => 'openweathermap',
        'location' => 'Paris',
        'api_key_ref' => 'secret:openweathermap_api_key',
    ]),
);
```

---

### Group (Composite)

Groupe de devices avec exécution parallèle ou séquentielle.

**ExecutionStrategy** :
- `BROADCAST` - Envoie à tous les devices en parallèle (fire-and-forget)
- `SEQUENTIAL` - Envoie un par un et attend chaque réponse
- `FIRST_RESPONSE` - Envoie en parallèle et retourne dès la première réponse
- `AGGREGATE` - Envoie en parallèle, attend toutes les réponses et agrège les résultats

**Exemples** :
- Groupe "Salon" (toutes les lampes du salon)
- Groupe "Étage" (tous les devices de l'étage)

**Création** :
```php
$group = Device::createGroup(
    label: new Label('Salon - Toutes les lampes'),
    childDeviceIds: [
        new DeviceId('device-lamp-1'),
        new DeviceId('device-lamp-2'),
        new DeviceId('device-lamp-3'),
    ],
    executionStrategy: ExecutionStrategy::BROADCAST,
);
```

**Exécution** :
```php
// Turn ON all devices in group (parallel)
$handler->__invoke(new ExecuteDeviceAction(
    deviceId: $group->id,
    capability: Capability::SWITCH,
    action: CapabilityAction::TURN_ON,
));

// Returns:
// [
//   'success' => true,
//   'strategy' => 'broadcast',
//   'sentCount' => 3,
//   'totalCount' => 3
// ]
```

---

### Scene (Composite)

Scène avec états spécifiques pour chaque device.

**Exemple** :
- Scène "Film" (lampes tamisées, volets fermés)
- Scène "Réveil" (lumière progressive, musique)

**Création** :
```php
$scene = Device::createScene(
    label: new Label('Soirée Film'),
    sceneStates: SceneStates::fromArray([
        'device-lamp-1' => [
            'switch' => ['value' => true],
            'brightness' => ['value' => 30],
        ],
        'device-lamp-2' => [
            'switch' => ['value' => false],
        ],
        'device-curtains-1' => [
            'position' => ['value' => 0],  // Closed
        ],
    ]),
    executionStrategy: ExecutionStrategy::SEQUENTIAL,
);
```

**Exécution** :
```php
// Activate scene (apply all states)
$handler->__invoke(new ExecuteDeviceAction(
    deviceId: $scene->id,
    capability: Capability::SCENE,
    action: CapabilityAction::ACTIVATE,
));
```

---

## ⚙️ ExecutionStrategy (Stratégies d'exécution composite)

Les devices composites (groupes/scènes) supportent 4 stratégies d'exécution différentes selon les besoins.

### 🔥 BROADCAST (Fire-and-forget)

**Comportement** :
- Envoie la commande à **tous les devices en parallèle**
- **Ne attend pas** les réponses (timeout: 0)
- Retourne immédiatement le nombre de commandes envoyées

**Cas d'usage** :
- Groupes de lampes (allumer/éteindre toutes les lampes)
- Actions où la confirmation n'est pas critique
- Performance maximale requise

**Exemple** :
```php
$lampGroup = Device::createComposite(
    label: new Label('Salon - Toutes les lampes'),
    compositeType: CompositeType::GROUP,
    childDeviceIds: [$lamp1, $lamp2, $lamp3],
    executionStrategy: ExecutionStrategy::BROADCAST,
    capabilities: [Capability::SWITCH],
);

// Execute
$result = $handler->__invoke(new ExecuteDeviceAction(
    deviceId: $lampGroup->id,
    capability: Capability::SWITCH,
    action: CapabilityAction::TURN_ON,
));

// Result:
// [
//   'success' => true,
//   'strategy' => 'broadcast',
//   'sentCount' => 3,
//   'totalCount' => 3
// ]
```

---

### ⏭️ SEQUENTIAL (Un par un)

**Comportement** :
- Envoie la commande **un device après l'autre**
- Attend la réponse de chaque device (timeout: 5000ms)
- Retourne tous les résultats détaillés

**Cas d'usage** :
- Scènes avec ordre d'exécution important
- Besoin de confirmation pour chaque action
- Debugging et traçabilité

**Exemple** :
```php
$bedtimeScene = Device::createComposite(
    label: new Label('Scène coucher'),
    compositeType: CompositeType::SCENE,
    childDeviceIds: [$shutters, $lights, $alarm],
    executionStrategy: ExecutionStrategy::SEQUENTIAL,  // Ordre important
    sceneStates: SceneStates::fromArray([
        'shutters' => ['position' => 0],      // 1. Fermer volets
        'lights' => ['switch' => false],       // 2. Éteindre lumières
        'alarm' => ['armed' => true],          // 3. Activer alarme
    ]),
);

// Result:
// [
//   'success' => true,
//   'strategy' => 'sequential',
//   'successCount' => 3,
//   'totalCount' => 3,
//   'results' => [
//     ['deviceId' => '...', 'success' => true, ...],
//     ['deviceId' => '...', 'success' => true, ...],
//     ['deviceId' => '...', 'success' => true, ...]
//   ]
// ]
```

---

### ⚡ FIRST_RESPONSE (Premier à répondre)

**Comportement** :
- Envoie la commande à **tous les devices en parallèle**
- **Retourne dès la première réponse** reçue
- Ignore les autres réponses qui arrivent après

**Cas d'usage** :
- **Capteurs redondants** (3 détecteurs de mouvement, retourne le premier qui détecte)
- **Failover** (plusieurs sources de données, prendre la première disponible)
- **Performance** (réduction latence pour actions critiques)

**Exemple** :
```php
$motionSensors = Device::createComposite(
    label: new Label('Détection mouvement - Salon'),
    compositeType: CompositeType::GROUP,
    childDeviceIds: [$sensor1, $sensor2, $sensor3],  // 3 capteurs redondants
    executionStrategy: ExecutionStrategy::FIRST_RESPONSE,
    capabilities: [Capability::MOTION],
);

// Execute
$result = $handler->__invoke(new ExecuteDeviceAction(
    deviceId: $motionSensors->id,
    capability: Capability::MOTION,
    action: CapabilityAction::READ_VALUE,
));

// Result:
// [
//   'success' => true,
//   'strategy' => 'first_response',
//   'responderId' => 'device-sensor-2',
//   'responderLabel' => 'Capteur mouvement 2',
//   'response' => true,  // Motion detected
//   'elapsedMs' => 120,  // A répondu en 120ms
//   'totalDevices' => 3
// ]
```

**Avantages** :
- ⚡ **Performance** : Latence minimale (répond dès le plus rapide)
- 🛡️ **Redondance** : Si un sensor est lent/offline, les autres peuvent répondre
- 💡 **Fail-fast** : Pas besoin d'attendre tous les devices

---

### 📊 AGGREGATE (Agrégation de résultats)

**Comportement** :
- Envoie la commande à **tous les devices en parallèle**
- **Attend toutes les réponses** (ou timeout 5s)
- **Agrège les résultats** selon le type de données :
  - **Numériques** (température, humidité) : **Moyenne**
  - **Booléens** (mouvement, contact) : **Consensus majoritaire**
  - **Autres types** : **Valeur la plus commune**

**Cas d'usage** :
- **Moyennes de capteurs** (température moyenne d'une pièce)
- **Consensus de vote** (détection fumée si ≥2/3 détecteurs alertent)
- **Agrégation d'énergie** (consommation totale d'une zone)

**Exemple 1 - Moyenne de températures** :
```php
$temperatureSensors = Device::createComposite(
    label: new Label('Température moyenne - Salon'),
    compositeType: CompositeType::GROUP,
    childDeviceIds: [$sensor1, $sensor2, $sensor3],
    executionStrategy: ExecutionStrategy::AGGREGATE,
    capabilities: [Capability::TEMPERATURE],
);

// Execute
$result = $handler->__invoke(new ExecuteDeviceAction(
    deviceId: $temperatureSensors->id,
    capability: Capability::TEMPERATURE,
    action: CapabilityAction::READ_VALUE,
));

// Result:
// [
//   'success' => true,
//   'strategy' => 'aggregate',
//   'aggregatedValue' => 22.5,  // (22 + 23 + 22.5) / 3
//   'aggregationType' => 'average',
//   'successCount' => 3,
//   'totalCount' => 3,
//   'results' => [
//     ['deviceId' => '...', 'value' => 22.0],
//     ['deviceId' => '...', 'value' => 23.0],
//     ['deviceId' => '...', 'value' => 22.5]
//   ]
// ]
```

**Exemple 2 - Consensus de détecteurs de fumée** :
```php
$smokeSensors = Device::createComposite(
    label: new Label('Détection fumée - Maison'),
    compositeType: CompositeType::GROUP,
    childDeviceIds: [$smoke1, $smoke2, $smoke3],
    executionStrategy: ExecutionStrategy::AGGREGATE,
    capabilities: [Capability::SMOKE],
);

// Si 2/3 détectent fumée → alarme déclenchée
// Result:
// [
//   'aggregatedValue' => true,  // Consensus: alarme
//   'aggregationType' => 'majority_consensus',
//   'results' => [
//     ['value' => true],   // Détecté
//     ['value' => true],   // Détecté
//     ['value' => false]   // Non détecté
//   ]
// ]
```

**Avantages** :
- 📊 **Données enrichies** : Moyenne, somme, consensus
- ⚡ **Performance** : Parallèle (plus rapide que SEQUENTIAL)
- 🎯 **Précision** : Combine plusieurs sources pour réduire les faux positifs

---

### 📋 Tableau comparatif

| Stratégie | Parallèle | Attend réponses | Agrégation | Retour | Cas d'usage |
|-----------|-----------|-----------------|------------|--------|-------------|
| **BROADCAST** | ✅ Oui | ❌ Non | ❌ Non | Nb envoyés | Groupes lampes, actions rapides |
| **SEQUENTIAL** | ❌ Non | ✅ Toutes | ❌ Non | Toutes les réponses | Scènes ordonnées, debugging |
| **FIRST_RESPONSE** | ✅ Oui | ⚡ Première | ❌ Non | 1ère réponse | Capteurs redondants, failover |
| **AGGREGATE** | ✅ Oui | ✅ Toutes | ✅ Oui | Valeur agrégée | Moyennes, consensus, totaux |

---

## 💻 Commandes principales

### Création de devices

```bash
# Via CLI (TODO)
php bin/console device:create physical \
    --label="Salon - Lampe" \
    --protocol=zigbee \
    --physical-address="living_room_light" \
    --manufacturer="Philips" \
    --model="Hue White"
```

### Exécution d'actions

Via API Platform ou CommandBus :

```php
$this->commandBus->dispatch(new ExecuteDeviceAction(
    deviceId: new DeviceId('device-123'),
    capability: Capability::SWITCH,
    action: CapabilityAction::TURN_ON,
    parameters: [],
));
```
    
--- 

## 🧪 Tests

```bash
# Tests unitaires
./vendor/bin/phpunit tests/Device/Domain/

# Tests d'intégration
./vendor/bin/phpunit tests/Device/Application/

# Test d'un device spécifique
./vendor/bin/phpunit tests/Device/Domain/Model/DeviceTest.php
```

---

---

## 🆕 Nouveautés et Changements

### Architecture refactorée

Le contexte Device a été refactoré pour une architecture plus flexible et conforme DDD :

#### 1. **Système de types**

```php
enum DeviceType {
    ACTUATOR,   // Device physique actionnable (lampe, prise, etc.)
    SENSOR,     // Device physique en lecture seule (capteur)
    COMPOSITE,  // Device composite (groupe ou scène)
    VIRTUAL     // Device virtuel (time, weather, http)
}

enum CompositeType {
    GROUP,      // Groupe de devices
    SCENE       // Scène avec états prédéfinis
}
```

**Avantages** :
- Distinction claire entre actuators (contrôlables) et sensors (lecture seule)
- Meilleure séparation des responsabilités
- Validation métier plus stricte

#### 2. **Composite Strategy**

Gestion flexible des groupes/scènes avec 3 stratégies :

```php
enum CompositeStrategy {
    NATIVE_IF_AVAILABLE, // Si le protocol gère les groupes ou scenes native il choisira le natif (valeur par défaut)
    NATIVE_ONLY,         // Force l'utilisation des groupes et scènes native. Si le protocol ne le supporte pas, erreur
    EMULATED_ONLY,       // Emulation géré par Marvin (pas de groupe natif)
    HYBRID,              // (Evolution future) Permet de gérer un mélange de groupe/scene native et non native
}
```

**Exemple - Groupe Zigbee natif** :
```php
$zigbeeGroup = Device::createComposite(
    label: new Label('Salon - Groupe Zigbee'),
    compositeType: CompositeType::GROUP,
    compositeStrategy: CompositeStrategy::NATIVE_ONLY,
    nativeGroupInfo: NativeGroupInfo::create(
        nativeGroupId: '[ID_GROUP]',  // Group ID Zigbee
        protocolId: '[ID_PROTOCOLE]',
        friendlyName: 'living_room_group'
    ),
    childDeviceIds: [],  // Géré par Zigbee directement
    capabilities: [Capability::SWITCH, Capability::BRIGHTNESS],
);
```

**Avantages** :
- Performance : Utilise les groupes natifs Zigbee/Z-Wave/Etc...
- Fiabilité : Pas besoin de boucle applicative

#### 3. **DeviceCapability simplifiée**

**Avant** :
```php
class DeviceCapability {
    private Capability $capability;
    private CapabilityCategory $category;
    private CapabilityStateDataType $dataType;
    private ?CapabilityState $state;
    private array $supportedActions;
}
```

**Maintenant** :
```php
class DeviceCapability {
    private Capability $capability;       // Type de capability
    private string $stateName;            // Nom du state spécifique
    private mixed $currentValue;          // Valeur actuelle (type flexible)
    private ?Metadata $metadata;          // Métadonnées (unit, etc.)
    private ?DateTimeImmutable $lastUpdatedAt;
}
```

**Avantages** :
- Plus simple et flexible
- Support natif des types mixtes (bool, int, float, string, array, object)
- Métadonnées extensibles
- Un DeviceCapability = un state précis

**Exemple** :
```php
// Thermostat avec plusieurs states pour la même capability
$device->addCapability(Capability::THERMOSTAT_MODE, 'current_heating_cooling_state', 'heating');
$device->addCapability(Capability::THERMOSTAT_MODE, 'target_heating_cooling_state', 'auto');
```

#### 4. **Mapping Doctrine organisé**

**Convention de nommage** :
- Entités/Aggregates : `Model.{EntityName}.orm.xml`
- Value Objects : `ValueObject.{ValueObjectName}.orm.xml`

**Exemple** :
```
src/Device/Infrastructure/Persistence/Doctrine/ORM/Mapping/
├── Model.Device.orm.xml
├── Model.DeviceCapability.orm.xml
├── Model.PendingAction.orm.xml
├── ValueObject.NativeGroupInfo.orm.xml
├── ValueObject.NativeSceneInfo.orm.xml
├── ValueObject.PhysicalAddress.orm.xml
├── ValueObject.SceneStates.orm.xml
├── ValueObject.TechnicalName.orm.xml
└── ValueObject.VirtualDeviceConfig.orm.xml
```

#### 5. **Protocol enum étendu**

Support de tous les protocoles :

```php
enum Protocol: string {
    case ZIGBEE = 'zigbee';
    case MQTT = 'mqtt';
    case REST = 'rest';
    case JSONRPC = 'jsonrpc';
    case WEBSOCKET = 'websocket';
    case BLUETOOTH = 'bluetooth';
    case NETWORK = 'network';
}
```

#### 6. **CapabilityAction étendu (150 actions)**

Le coverage des actions a été porté à **100% pour tous les adapters** :

- **Zigbee2MQTT** : 96 actions
- **Tasmota** : 40 actions
- **Shelly Gen1/Gen2/MQTT** : 25 actions chacun
- **Bluetooth2MQTT** : 20 actions

Catégories d'actions :
- **Lighting** : turn_on/off, set_brightness, set_color_rgb/hsv/hex, set_color_temp, effects
- **Climate** : set_temperature, set_heating/cooling_setpoint, fan controls, humidifier
- **Covers** : open, close, stop, set_position, set_tilt
- **Security** : lock, unlock, trigger, arm_away/home/night
- **Media** : play, pause, volume, input_select, channel
- **Cameras** : snapshot, record, ptz controls
- **Notifications** : send_notification, play_sound, flash_light
- **Scenes** : activate, recall, store, delete
- **System** : identify, configure, reset

#### 7. **ExecutionStrategy complètes (4 stratégies)**

Toutes les stratégies d'exécution sont maintenant implémentées :

```php
enum ExecutionStrategy: string {
    case BROADCAST = 'broadcast';        // ✅ Fire-and-forget parallèle
    case SEQUENTIAL = 'sequential';      // ✅ Un par un
    case FIRST_RESPONSE = 'first_response';  // 🆕 Premier à répondre
    case AGGREGATE = 'aggregate';        // 🆕 Agrégation de résultats
}
```

**FIRST_RESPONSE** :
- Envoie en parallèle, retourne dès la première réponse
- Cas d'usage : Capteurs redondants, failover, latence minimale
- Retour : Première réponse + device qui a répondu + temps de réponse

**AGGREGATE** :
- Envoie en parallèle, attend toutes les réponses et agrège
- Agrégation automatique selon le type :
  - Numériques → Moyenne
  - Booléens → Consensus majoritaire
  - Autres → Valeur la plus commune
- Cas d'usage : Moyennes de capteurs, consensus, totaux

Voir la section **ExecutionStrategy** pour plus de détails et exemples.

---

## 📚 Documentation complémentaire

- **Doctrine Mappings** : `src/Device/Infrastructure/Persistence/Doctrine/ORM/Mapping/`
- **translations/device.fr.yaml** : Traductions françaises (38 exceptions)
- **migrations/** : Migrations Doctrine

---

## 🚀 Exemples d'utilisation

### Créer un device physique

```php
use Marvin\Device\Application\Command\Device\CreatePhysicalDevice;

$command = new CreatePhysicalDevice(
    label: 'Salon - Lampe principale',
    protocol: 'zigbee',
    physicalAddress: 'living_room_light',
    protocolId: 'protocol-zigbee-001',
    zoneId: 'zone-salon',
    manufacturer: 'Philips',
    model: 'Hue White and Color',
    firmwareVersion: '1.88.1',
);

$this->commandBus->dispatch($command);
```

### Exécuter une action

```php
use Marvin\Device\Application\Command\Device\ExecuteDeviceAction;

$command = new ExecuteDeviceAction(
    deviceId: new DeviceId('device-123'),
    capability: Capability::BRIGHTNESS,
    action: CapabilityAction::SET_BRIGHTNESS,
    parameters: ['brightness' => 200],
);

$result = $this->commandBus->dispatch($command);
// ['success' => true, 'strategy' => 'single', 'results' => [...]]
```

### Créer un groupe

```php
use Marvin\Device\Application\Command\Group\CreateGroup;

$command = new CreateGroup(
    label: 'Salon - Toutes les lampes',
    childDeviceIds: [
        'device-lamp-1',
        'device-lamp-2',
        'device-lamp-3',
    ],
    executionStrategy: 'broadcast',
);

$this->commandBus->dispatch($command);
```

### Créer une scène

```php
use Marvin\Device\Application\Command\Scene\CreateScene;

$command = new CreateScene(
    label: 'Soirée Film',
    sceneStates: [
        'device-lamp-1' => [
            'switch' => ['value' => true],
            'brightness' => ['value' => 30],
        ],
        'device-curtains-1' => [
            'position' => ['value' => 0],
        ],
    ],
    executionStrategy: 'sequential',
);

$this->commandBus->dispatch($command);
```

---

## 🔧 Configuration avancée

### Timeout PendingAction

Par défaut : 5 secondes

```php
// Dans ProtocolCapabilityService
private const int DEFAULT_TIMEOUT_MS = 5000;
private const int POLLING_INTERVAL = 100;  // 100ms between polls
```

Pour modifier :
```php
$result = $this->protocolCapability->executeAction(
    // ...
    timeout: 10000  // 10 secondes
);
```

### Cleanup PendingActions expirées

TODO : Créer une commande Symfony pour nettoyer les PendingActions anciennes.

```bash
# À implémenter
php bin/console device:pending-action:cleanup --older-than=24h
```

---

## 🤝 Contribution

### Ajouter une nouvelle capability

1. **Ajouter dans** `Domain/ValueObject/Capability.php` (enum)
2. **Ajouter les states associés dans** `Domain/ValueObject/CapabilityState.php`
3. **Ajouter les actions dans** `Domain/ValueObject/CapabilityAction.php`
4. **Définir les contraintes de validation** dans `CapabilityState::getConstraints()`
5. **Ajouter les traductions** dans `translations/device.fr.yaml`
6. **Implémenter dans les Protocol Adapters** concernés
7. **Créer les tests unitaires**

**Exemple - Ajouter support d'un purificateur d'air** :

```php
// 1. Domain/ValueObject/Capability.php
enum Capability: string
{
    // ...
    case AIR_PURIFIER = 'air_purifier';
}

// 2. Domain/ValueObject/CapabilityState.php
enum CapabilityState: string
{
    // ...
    case AIR_PURIFIER_MODE = 'air_purifier_mode';  // auto, manual, sleep
    case AIR_PURIFIER_SPEED = 'air_purifier_speed';  // 1-10
    case AIR_FILTER_LIFE = 'air_filter_life';  // 0-100%

    public function getConstraints(): CapabilityStateConstraints
    {
        return match ($this) {
            // ...
            self::AIR_PURIFIER_MODE => CapabilityStateConstraints::enum(['auto', 'manual', 'sleep']),
            self::AIR_PURIFIER_SPEED => CapabilityStateConstraints::integer(min: 1, max: 10),
            self::AIR_FILTER_LIFE => CapabilityStateConstraints::percentage(),
        };
    }
}

// 3. Domain/ValueObject/CapabilityAction.php
enum CapabilityAction: string
{
    // ...
    case SET_PURIFIER_MODE = 'set_purifier_mode';
    case SET_PURIFIER_SPEED = 'set_purifier_speed';
    case RESET_FILTER_LIFE = 'reset_filter_life';
}

// 4. translations/device.fr.yaml
device:
  capability:
    air_purifier: "Purificateur d'air"
  action:
    set_purifier_mode: "Définir mode purificateur"
    set_purifier_speed: "Définir vitesse purificateur"

// 5. Protocol/Infrastructure/Adapter/Zigbee2MqttAdapter.php
private function buildCommand(string $action, array $parameters): array
{
    return match ($action) {
        // ...
        'set_purifier_mode' => ['air_purifier_mode' => $parameters['mode'] ?? 'auto'],
        'set_purifier_speed' => ['air_purifier_speed' => $parameters['speed'] ?? 5],
    };
}
```

### Ajouter un nouveau Protocol Adapter

Voir le contexte Protocol : `src/Protocol/README.md`
