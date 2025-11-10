# Protocol Context

**Bounded Context** pour la gestion des protocoles de communication avec les devices domotiques.

## 📋 Vue d'ensemble

Le contexte Protocol gère la communication avec les devices via différents protocoles (MQTT, REST, JSON-RPC, WebSocket). Il transforme les messages bruts des protocols en événements de domaine et envoie des commandes aux devices.

### Responsabilités

✅ **Ce que Protocol fait** :
- Gestion des protocoles de transport (MQTT, REST, JSON-RPC, WebSocket)
- Transformation messages externes → événements de domaine (ACL)
- Envoi de commandes vers devices avec 3 modes d'exécution
- Gestion des adapters par type de device (Zigbee2MQTT, Shelly, Tasmota, etc.)
- Support Correlation ID (MQTT v5, JSON-RPC natif)
- Support Device Lock (protocoles sans correlation)
- Mode Fire & Forget (asynchrone)

❌ **Ce que Protocol NE fait PAS** :
- Gestion du cycle de vie des devices (→ Device Context)
- Stockage des credentials (→ Secret Context)
- Historique des données (→ Telemetry Context)
- Logique métier domotique (→ Automation Context)

---

## 🏗️ Architecture

### Structure DDD

```
src/Protocol/
├── Domain/                     # Logique métier
│   ├── Model/
│   │   ├── Protocol.php        # Aggregate Root
│   │   └── ProtocolAdapterInterface.php
│   ├── ValueObject/
│   │   ├── ProtocolId.php
│   │   ├── ProtocolType.php
│   │   ├── ProtocolStatus.php
│   │   └── ExecutionMode.php
│   ├── Event/
│   │   ├── ProtocolRegistered.php
│   │   ├── ProtocolStatusChanged.php
│   │   └── ProtocolCommandSent.php
│   └── Exception/
│       ├── ProtocolNotFoundException.php
│       ├── DeviceTimeoutException.php
│       └── ... (6 exceptions)
│
├── Application/                # Use cases
│   ├── Command/
│   ├── CommandHandler/
│   ├── Query/
│   ├── QueryHandler/
│   ├── Service/               # ACL Interfaces
│   │   ├── DeviceQueryServiceInterface.php
│   │   └── SecretQueryServiceInterface.php
│   └── DTO/
│
├── Infrastructure/
│   ├── Protocol/              # Implémentations protocoles
│   │   ├── MqttProtocol.php   (simps/mqtt, MQTT v5)
│   │   ├── RestProtocol.php   (Symfony HttpClient)
│   │   ├── JsonRpcProtocol.php
│   │   └── WebSocketProtocol.php (Swoole)
│   │
│   ├── Adapter/               # Adapters par device
│   │   ├── Zigbee2MqttAdapter.php
│   │   ├── TasmotaAdapter.php
│   │   ├── ShellyGen1Adapter.php
│   │   ├── ShellyGen2Adapter.php
│   │   ├── ShellyMqttAdapter.php
│   │   └── Bluetooth2MqttAdapter.php
│   │
│   ├── Listener/
│   │   ├── MqttDeviceStateListener.php
│   │   └── MqttDeviceResponseListener.php
│   │
│   ├── Service/               # ACL Implémentations
│   │   ├── DeviceQueryService.php
│   │   └── SecretQueryService.php
│   │
│   └── Framework/
│       └── Symfony/
│           └── Command/       # CLI Commands
│
└── Presentation/
    └── Cli/
```

---

## 🔌 Adapters disponibles

### Zigbee2MqttAdapter
**Protocol** : MQTT (via bridge Zigbee2MQTT)

**Devices supportés** :
- Capteurs Aqara (température, humidité, mouvement)
- Ampoules Philips Hue, IKEA Trådfri
- Interrupteurs Xiaomi
- Tous devices ZigBee compatibles Zigbee2MQTT

**physicalAddress** : Friendly name (ex: `living_room_light`)

**Topics MQTT** :
```
zigbee2mqtt/{friendly_name}       # État device
zigbee2mqtt/{friendly_name}/set   # Commandes
```

**Mode par défaut** : `DEVICE_LOCK` (Zigbee2MQTT ne supporte pas MQTT v5 correlation)

---

### TasmotaAdapter
**Protocol** : MQTT

**Devices supportés** :
- Sonoff (relais, interrupteurs)
- ESP8266/ESP32 avec firmware Tasmota
- Prises connectées Tasmota

**physicalAddress** : Topic device (ex: `tasmota_ABC123`)

**Topics MQTT** :
```
cmnd/{device_id}/POWER     # Commandes
stat/{device_id}/RESULT    # Résultats
tele/{device_id}/STATE     # États
```

**Mode par défaut** : `CORRELATION_ID` (si broker MQTT v5)

---

### ShellyGen1Adapter
**Protocol** : REST (HTTP)

**Devices supportés** :
- Shelly 1, 1PM, 2.5
- Shelly Plug S
- Shelly RGBW2

**physicalAddress** : IP ou hostname (ex: `192.168.1.100` ou `shelly1-ABC123`)

**Endpoints REST** :
```
GET  /status               # État global
GET  /relay/0              # État relay
GET  /relay/0?turn=on      # Commande
```

**Mode par défaut** : `DEVICE_LOCK` (REST synchrone)

---

### ShellyGen2Adapter
**Protocol** : JSON-RPC over HTTP

**Devices supportés** :
- Shelly Plus 1, Plus 1PM, Plus 2PM
- Shelly Pro 1, Pro 2, Pro 3
- Shelly Plus i4

**physicalAddress** : IP ou hostname (ex: `192.168.1.100`)

**Endpoint** :
```
POST /rpc
{
  "id": 1,
  "method": "Switch.Set",
  "params": {"id": 0, "on": true}
}
```

**Mode par défaut** : `CORRELATION_ID` (JSON-RPC natif avec `id`)

---

### ShellyMqttAdapter
**Protocol** : MQTT

**Devices supportés** : Shelly Gen1/Gen2/Gen3 configurés en mode MQTT

**physicalAddress** : Device ID (ex: `shellyplus1-ABC123`)

**Topics MQTT** :
```
shellies/{device_id}/relay/0          # État
shellies/{device_id}/relay/0/command  # Commande
```

**Mode par défaut** : `CORRELATION_ID` (dépend configuration)

---

### Bluetooth2MqttAdapter
**Protocol** : MQTT (via ESP32 Bluetooth Proxy)

**Devices supportés** :
- Capteurs Xiaomi Mi (température, humidité)
- Ruuvi Tag
- Thermomètres Inkbird
- Serrures BLE (Nuki, August, Yale)

**physicalAddress** : MAC address (ex: `AA:BB:CC:DD:EE:FF`)

**Topics MQTT** :
```
marvin/bluetooth/{mac_address}      # États
marvin/bluetooth/{mac_address}/set  # Commandes
```

**Mode par défaut** : `DEVICE_LOCK`

---

## ⚙️ Modes d'exécution

### 1. FIRE_AND_FORGET (Asynchrone)

**Principe** : Envoie la commande et retourne immédiatement, sans attendre de réponse.

**Caractéristiques** :
- ✅ Ultra-rapide (pas d'attente)
- ✅ Parallèle (pas de lock)
- ✅ Idéal pour UI réactive
- ❌ Pas de retour de résultat
- ❌ Pas de garantie de succès

**Flow** :
```
1. Envoie commande MQTT/REST
2. Return immédiatement
3. (Plus tard) DeviceStateChanged event
```

**Cas d'usage** :
- Boutons ON/OFF dans l'interface
- Scénarios sans validation
- Commandes "best effort"

---

### 2. CORRELATION_ID (Synchrone avec correlation)

**Principe** : Génère un correlationId unique, envoie la commande avec ce correlation, et attend la réponse sur un topic spécifique.

**Caractéristiques** :
- ✅ Synchrone (bloquant)
- ✅ Retour de résultat garanti
- ✅ Parallèle (plusieurs commandes simultanées)
- ✅ Performant (pas de lock)
- ⚠️ Nécessite support protocol (MQTT v5 ou JSON-RPC)

**Flow** :
```
1. Generate correlationId
2. Create PendingAction(correlationId, WAITING)
3. Subscribe to "marvin/response/{correlationId}"
4. Publish command with correlation_data (MQTT v5)
5. Wait for response (polling PendingAction)
6. MqttDeviceResponseListener receives response
7. Complete PendingAction
8. Return result
```

**Cas d'usage** :
- API REST avec validation
- Scripts nécessitant confirmation
- Commandes critiques

**Adapters supportés** :
- ✅ ShellyGen2Adapter (JSON-RPC natif)
- ⚠️ TasmotaAdapter (si broker MQTT v5)
- ⚠️ ShellyMqttAdapter (dépend config)

**Adapters NON supportés** :
- ❌ Zigbee2MqttAdapter (Zigbee2MQTT ne supporte pas MQTT v5 correlation)
- ❌ ShellyGen1Adapter (REST simple)
- ❌ Bluetooth2MqttAdapter (proxy ESP32 simple)

---

### 3. DEVICE_LOCK (Synchrone avec lock)

**Principe** : Verrouille le device, envoie la commande, attend le changement d'état, puis libère le lock.

**Caractéristiques** :
- ✅ Synchrone (bloquant)
- ✅ Retour de résultat garanti
- ✅ Fonctionne avec TOUS les protocols
- ❌ Séquentiel (une commande à la fois par device)
- ⚠️ Moins performant si multiples commandes

**Flow** :
```
1. Check if device locked
2. Create PendingAction(deviceId, WAITING) → LOCK
3. Publish command
4. Wait for DeviceStateChanged event
5. MqttDeviceStateListener completes PendingAction → UNLOCK
6. Return result
```

**Cas d'usage** :
- Fallback universel
- Protocols sans correlation
- Commandes séquentielles critiques

**Adapters supportés** :
- ✅ TOUS les adapters (mode universel)

---

### Tableau comparatif

| Mode | Synchrone | Retour | Performance | Parallèle | Universalité |
|------|-----------|--------|-------------|-----------|--------------|
| **FIRE_AND_FORGET** | ❌ Non | ❌ Pas de retour | ⚡⚡⚡⚡ | ✅ Oui | ✅ Tous |
| **CORRELATION_ID** | ✅ Oui | ✅ Résultat | ⚡⚡⚡ | ✅ Oui | ⚠️ Limité |
| **DEVICE_LOCK** | ✅ Oui | ✅ Résultat | ⚡⚡ | ❌ Non | ✅ Tous |

---

## 💻 Commandes CLI

### protocol:mqtt:listen
Écoute les messages MQTT et les transforme en événements de domaine.

```bash
# Écouter tous les topics
php bin/console protocol:mqtt:listen

# Écouter des topics spécifiques
php bin/console protocol:mqtt:listen --topics="zigbee2mqtt/#,shellies/#"

# Avec timeout
php bin/console protocol:mqtt:listen --timeout=3600
```

**Supervisord** : Voir `docker/worker/config/conf.d/protocol_mqtt_listener_worker.conf`

---

### protocol:mqtt:publish
Publie un message sur un topic MQTT.

```bash
# Publier une commande
php bin/console protocol:mqtt:publish "zigbee2mqtt/salon_lamp/set" '{"state":"ON"}'

# Avec correlation ID
php bin/console protocol:mqtt:publish "zigbee2mqtt/salon_lamp/set" '{"state":"ON"}' --correlation-id="abc123"
```

---

### protocol:websocket:listen
Écoute les messages WebSocket d'un serveur.

```bash
# Écouter WebSocket Shelly
php bin/console protocol:websocket:listen ws://192.168.1.100/rpc

# Avec SSL
php bin/console protocol:websocket:listen wss://192.168.1.100/rpc
```

**Supervisord** : Voir `docker/worker/config/conf.d/protocol_websocket_listener_worker.conf`

---

### protocol:websocket:send
Envoie un message à un serveur WebSocket.

```bash
# Envoyer et attendre réponse
php bin/console protocol:websocket:send ws://192.168.1.100/rpc '{"id":1,"method":"Switch.Set","params":{"id":0,"on":true}}'
```

---

### protocol:websocket:connect
Test la connexion WebSocket vers un device.

```bash
php bin/console protocol:websocket:connect ws://192.168.1.100/rpc
```

---

### protocol:adapter:list
Liste tous les adapters disponibles.

```bash
# Tous les adapters
php bin/console protocol:adapter:list

# Filtrer par type
php bin/console protocol:adapter:list --type=mqtt
```

---

### protocol:adapter:test
Teste un adapter en envoyant une commande.

```bash
php bin/console protocol:adapter:test zigbee2mqtt salon_lamp turn_on
```

---

## ⚙️ Configuration

### Variables d'environnement (.env)

```bash
###> MQTT Configuration ###
MQTT_HOST=mosquitto
MQTT_PORT=1883
MQTT_USER=marvin
MQTT_PASSWORD=your_password
MQTT_PROTOCOL_LEVEL=5          # MQTT v5 (ou 4 pour MQTT v3.1.1)
MQTT_USE_SSL=false
MQTT_SSL_ALLOW_SELF_SIGNED=false
MQTT_SSL_VERIFY_PEER=false
MQTT_SSL_CA_FILE=
MQTT_SSL_CERT_FILE=
MQTT_SSL_KEY_FILE=

###> WebSocket Configuration ###
WEBSOCKET_HOST=192.168.1.100
WEBSOCKET_PORT=80
WEBSOCKET_USE_SSL=false
```

### Services Symfony (config/services/protocol.php)

```php
// Adapters auto-tagged
Marvin\Protocol\Infrastructure\Adapter\:
    resource: '../src/Protocol/Infrastructure/Adapter/*'
    tags: ['protocol.adapter']

// Listeners
Marvin\Protocol\Infrastructure\Listener\MqttDeviceStateListener:
    arguments:
        $adapters: !tagged_iterator protocol.adapter
```

---

## 🔗 Intégrations Cross-Context (ACL)

### Protocol → Device Context

**Interface** : `DeviceQueryServiceInterface`

```php
interface DeviceQueryServiceInterface
{
    public function getDevice(string $deviceId): DeviceDTO;
    public function getDeviceNativeId(string $deviceId): string;
    public function getDeviceProtocol(string $deviceId): string;
}
```

**Implémentation** : `DeviceQueryService` utilise le `DeviceRepository` du Device Context.

**Mapping** :
- `DeviceDTO->nativeId` = `Device->physicalAddress->value`
- Le physicalAddress du Device Context devient le nativeId dans Protocol Context

---

### Device → Protocol Context

**Interface** : `ProtocolCapabilityServiceInterface` (dans Device Context)

```php
interface ProtocolCapabilityServiceInterface
{
    public function executeAction(
        string $protocolId,
        string $nativeId,
        string $capability,
        string $action,
        array $parameters,
        int $timeout
    ): array;
}
```

**Implémentation** : `ProtocolCapabilityService` gère :
- Choix du mode d'exécution (CORRELATION_ID → DEVICE_LOCK fallback)
- Création et gestion PendingAction
- Polling pour attendre la réponse
- Timeout handling

---

### Protocol → Secret Context

**Interface** : `SecretQueryServiceInterface`

```php
interface SecretQueryServiceInterface
{
    public function getSecret(string $key): string;
    public function getMqttCredentials(): MqttCredentialsDTO;
    public function getWebSocketCredentials(string $url): ?WebSocketCredentialsDTO;
}
```

**Implémentation** : Utilise le `SecretRepository` pour récupérer credentials chiffrés.

---

## ⚠️ Limitations importantes

### Zigbee2MQTT et MQTT v5 Correlation

**Problème** : Zigbee2MQTT **ne supporte PAS** nativement les correlation IDs MQTT v5.

**Architecture Zigbee2MQTT** :
```
Request:  zigbee2mqtt/{friendly_name}/set
Response: zigbee2mqtt/{friendly_name}  (état général, pas de correlation)
```

**Conséquences** :
- ❌ Impossible d'utiliser `CORRELATION_ID` avec Zigbee2MQTT
- ❌ Pas de matching 1:1 entre commande et réponse
- ❌ Topics request et response différents

**Solution** : Utiliser `DEVICE_LOCK` par défaut pour Zigbee2MQTT.

Le mode `CORRELATION_ID` fonctionne uniquement avec :
- ✅ Shelly Gen2/Gen3 (JSON-RPC natif avec `id`)
- ⚠️ Tasmota (si broker MQTT v5 configuré)

---

## 📚 Documentation complémentaire

- **CLAUDE.md** : Spécification technique complète
- **TODO.md** : Roadmap et tâches restantes
- **translations/protocol.fr.yaml** : Traductions françaises
- **docker/worker/config/conf.d/protocol_*.conf** : Config Supervisord

---

## 🧪 Tests

```bash
# Tests unitaires
./vendor/bin/phpunit tests/Protocol/

# Tests d'intégration (TODO)
# Tester flow MQTT complet
# Tester PendingAction avec 3 modes
# Tester timeout et retry
```

---

## 🚀 Démarrage rapide

### 1. Configuration

Copier `.env` → `.env.local` et configurer MQTT :

```bash
MQTT_HOST=mosquitto
MQTT_PORT=1883
MQTT_USER=marvin
MQTT_PASSWORD=your_password
```

### 2. Démarrer les workers

```bash
# Activer le worker MQTT dans Supervisord
# Décommenter les lignes dans docker/worker/config/conf.d/protocol_mqtt_listener_worker.conf

sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start marvin-protocol-mqtt-listener
```

### 3. Tester

```bash
# Lister les adapters
php bin/console protocol:adapter:list

# Publier un message test
php bin/console protocol:mqtt:publish "zigbee2mqtt/test/set" '{"state":"ON"}'

# Écouter les messages (dans un autre terminal)
php bin/console protocol:mqtt:listen
```

---

## 🤝 Contribution

Pour ajouter un nouvel adapter :

1. Créer la classe dans `Infrastructure/Adapter/`
2. Implémenter `ProtocolAdapterInterface`
3. Définir `sendCommand()` et `transformMessage()`
4. Tagger avec `protocol.adapter` dans `config/services/protocol.php`
5. Ajouter les traductions dans `translations/protocol.fr.yaml`

Exemple minimal :

```php
final readonly class MyAdapter implements ProtocolAdapterInterface
{
    public function getName(): string { return 'my_adapter'; }

    public function getSupportedProtocols(): array { return ['mqtt']; }

    public function supports(string $protocol, array $deviceMetadata = []): bool
    {
        return $protocol === 'mqtt' && ($deviceMetadata['adapter'] ?? '') === 'my_adapter';
    }

    public function sendCommand(string $nativeId, string $action, array $parameters = [], ExecutionMode $mode = ExecutionMode::DEVICE_LOCK, ?CorrelationId $correlationId = null): ?array
    {
        // Implémenter l'envoi de commande
    }

    public function transformMessage(string $topic, array $payload): ?array
    {
        // Transformer message protocol → event domaine
    }

    public function getDefaultExecutionMode(): ExecutionMode
    {
        return ExecutionMode::DEVICE_LOCK;
    }

    public function supportsCorrelation(): bool { return false; }
}
```
