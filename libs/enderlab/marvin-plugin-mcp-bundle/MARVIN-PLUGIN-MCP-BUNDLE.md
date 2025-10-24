# MARVIN-PLUGIN-MCP-BUNDLE - Technical Documentation

## Vue d'ensemble

Le **marvin-plugin-mcp-bundle** est une bibliothèque qui intègre le protocole **MCP (Model Context Protocol)** dans Marvin. Elle expose des outils MCP permettant aux assistants IA (Claude, ChatGPT, etc.) d'interagir avec Marvin et d'obtenir des informations contextuelles sur la syntaxe des Flows, la configuration des devices, et d'autres aspects du système domotique.

## Qu'est-ce que MCP ?

**MCP (Model Context Protocol)** est un protocole standardisé développé par Anthropic pour permettre aux Large Language Models (LLMs) d'accéder à des sources de données externes et d'exécuter des actions via des "tools" (outils).

### Concepts MCP

- **Tool** : Une fonction exposée au LLM pour récupérer des informations ou exécuter une action
- **Prompt** : Contexte ou instructions prédéfinies pour guider le LLM
- **Resource** : Données accessibles via des URIs

Ce bundle se concentre sur les **Tools MCP** pour documenter et aider les LLMs à générer du code/configuration Marvin.

---

## Architecture

### Structure du bundle

```
src/
├── McpBundle.php                    # Bundle principal
└── Mcp/
    └── Application/
        └── Tools/
            └── DescribeFlowYamlTool.php    # MCP Tool pour la syntaxe Flow YAML
```

**Note:** Le bundle est actuellement minimal avec un seul outil. Il est conçu pour être étendu avec d'autres tools selon les besoins.

---

## MCP Tools

### 1. DescribeFlowYamlTool

**Fichier:** `src/Mcp/Application/Tools/DescribeFlowYamlTool.php`

Outil MCP qui fournit la documentation complète de la syntaxe YAML utilisée pour définir les Flows Marvin.

**Attribut MCP:**
```php
#[McpTool(
    name: 'describe-flow-yaml',
    description: 'Décrit la syntaxe YAML complète utilisée pour définir un Flow Marvin.',
)]
class DescribeFlowYamlTool
{
    public function __invoke(): array
    {
        return [
            'syntax' => $description,      // Documentation YAML complète
            'format' => 'yaml',
            'purpose' => 'Aide les IA à générer du YAML conforme pour les Flows Marvin.'
        ];
    }
}
```

**Retour:**
- **`syntax`** (string) : Documentation YAML complète avec exemples
- **`format`** (string) : Format du contenu (`yaml`)
- **`purpose`** (string) : Description de l'utilité de cet outil

---

### Syntaxe Flow YAML Documentée

Le tool expose la syntaxe suivante pour les Flows Marvin :

#### Structure de base

```yaml
flow:
  name: "Fermer les volets la nuit"
  description: "Ferme tous les volets au coucher du soleil."
  enabled: true

  triggers:
    - type: time
      at: "22:00"
    - type: sun
      event: "sunset"

  conditions:
    - type: weather
      key: "rain"
      operator: "not_equals"
      value: true
    - type: state
      device: "presence.bureau"
      equals: false

  actions:
    - type: device
      device: "volets.salon"
      command: "close"
    - type: delay
      seconds: 10
    - type: device
      device: "volets.chambre"
      command: "close"
```

#### Champs supportés

**Flow:**
```yaml
flow:
  name: string              # Nom du flow (requis)
  description: string?      # Description optionnelle
  enabled: boolean          # Activé ou non (requis)
  triggers: Trigger[]       # Liste des déclencheurs (requis)
  conditions: Condition[]   # Liste des conditions (optionnel)
  actions: Action[]         # Liste des actions (requis)
```

**Trigger:**
```yaml
Trigger:
  type: string              # time | sun | weather | state | mqtt | event
  at?: string               # Heure (ex: "22:00") pour type=time
  event?: string            # "sunrise" | "sunset" pour type=sun
  topic?: string            # Topic MQTT pour type=mqtt
  key?: string              # Clé de l'état
  operator?: string         # Opérateur de comparaison
  value?: mixed             # Valeur de comparaison
```

**Condition:**
```yaml
Condition:
  type: string              # state | weather | numeric | custom
  device?: string           # ID du device
  key?: string              # Clé de l'état ou météo
  operator?: string         # equals | not_equals | gt | lt | in | not_in
  value?: mixed             # Valeur de comparaison
```

**Action:**
```yaml
Action:
  type: string              # device | flow | http | mqtt | delay | script
  device?: string           # ID du device (pour type=device)
  command?: string          # Commande à exécuter
  data?: mixed              # Données additionnelles
  flow?: string             # ID d'un autre flow (pour type=flow)
  url?: string              # URL HTTP (pour type=http)
  method?: string           # Méthode HTTP (GET, POST, etc.)
  body?: mixed              # Corps de la requête HTTP
  seconds?: int             # Délai en secondes (pour type=delay)
```

#### Exemple minimal

```yaml
flow:
  name: "Allumer la lumière quand mouvement"
  triggers:
    - type: state
      device: "motion.salon"
      equals: true
  actions:
    - type: device
      device: "light.salon"
      command: "on"
```

---

## Usage avec un LLM

### Scénario: Génération d'un Flow via Claude Desktop

**1. Configuration MCP dans Claude Desktop**

```json
{
  "mcpServers": {
    "marvin": {
      "command": "php",
      "args": [
        "/path/to/marvin-core/bin/console",
        "mcp:serve"
      ]
    }
  }
}
```

**2. Interaction avec Claude**

**Utilisateur:**
> "Crée-moi un flow qui allume les lumières du salon à 18h seulement s'il fait nuit et que je suis à la maison."

**Claude utilise le tool `describe-flow-yaml`:**

Le LLM appelle automatiquement `describe-flow-yaml` pour récupérer la syntaxe, puis génère :

```yaml
flow:
  name: "Lumières salon en soirée"
  description: "Allume les lumières du salon à 18h si présent et nuit"
  enabled: true
  
  triggers:
    - type: time
      at: "18:00"
  
  conditions:
    - type: sun
      event: "sunset"
    - type: state
      device: "presence.home"
      equals: true
  
  actions:
    - type: device
      device: "light.salon"
      command: "on"
```

**3. Validation et enregistrement**

Le YAML généré peut ensuite être :
- Enregistré dans Marvin via API
- Validé contre un schéma JSON/YAML
- Activé immédiatement

---

## Extension du Bundle

### Ajouter un nouveau MCP Tool

**Exemple: Tool pour lister les devices**

```php
namespace EnderLab\MarvinPluginMcpBundle\Mcp\Application\Tools;

use Mcp\Capability\Attribute\McpTool;
use Marvin\Device\Domain\Repository\DeviceRepositoryInterface;

#[McpTool(
    name: 'list-devices',
    description: 'Liste tous les devices disponibles dans Marvin avec leurs capacités.',
)]
final readonly class ListDevicesTool
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository
    ) {}

    public function __invoke(): array
    {
        $devices = $this->deviceRepository->findAll();
        
        $deviceList = array_map(function($device) {
            return [
                'id' => $device->id->toString(),
                'name' => $device->name->value,
                'type' => $device->type->value,
                'capabilities' => $device->capabilities->toArray(),
            ];
        }, $devices);

        return [
            'devices' => $deviceList,
            'count' => count($deviceList),
            'purpose' => 'Permet aux IA de connaître les devices disponibles pour créer des flows.'
        ];
    }
}
```

**Usage dans un LLM:**

**Utilisateur:**
> "Montre-moi tous les devices de type 'light' disponibles."

**Claude utilise `list-devices`**, filtre les résultats, et répond :
> "Voici les lumières disponibles :
> - light.salon
> - light.chambre
> - light.cuisine
> ..."

---

### Ajouter un MCP Prompt

**Exemple: Prompt pour guider la création de flows**

```php
namespace EnderLab\MarvinPluginMcpBundle\Mcp\Application\Prompts;

use Mcp\Capability\Attribute\McpPrompt;

#[McpPrompt(
    name: 'create-flow-guide',
    description: 'Guide pour créer un flow Marvin étape par étape.',
)]
final class CreateFlowGuidePrompt
{
    public function __invoke(): string
    {
        return <<<PROMPT
Tu es un assistant spécialisé dans la création de Flows Marvin.

Lorsqu'un utilisateur te demande de créer un flow, suis ces étapes :

1. **Comprendre l'intention**
   - Quel est le déclencheur ? (temps, événement, état d'un device, etc.)
   - Quelles sont les conditions ? (optionnel)
   - Quelles actions doivent être exécutées ?

2. **Utiliser le tool `describe-flow-yaml`**
   - Récupère la syntaxe complète des flows

3. **Générer le YAML**
   - Respecte strictement la syntaxe documentée
   - Utilise des noms de devices réels (via `list-devices` si nécessaire)
   - Ajoute des commentaires pour expliquer la logique

4. **Valider**
   - Vérifie que tous les champs requis sont présents
   - Vérifie la cohérence des types (time, sun, device, etc.)

5. **Proposer des améliorations**
   - Suggère des conditions supplémentaires si pertinent
   - Propose des actions alternatives

Exemple de réponse structurée :
```yaml
# Voici le flow demandé :
flow:
  name: "..."
  # ...
```

Explications :
- Le trigger X déclenche le flow quand...
- La condition Y garantit que...
- L'action Z exécute...
PROMPT;
    }
}
```

---

## Configuration

### 1. Serveur MCP

**Commande pour démarrer le serveur MCP:**
```bash
php bin/console mcp:serve
```

**Options:**
```bash
php bin/console mcp:serve --host=127.0.0.1 --port=8080
```

### 2. Enregistrement des Tools

Les tools sont automatiquement découverts via l'attribut `#[McpTool]` grâce à l'autoconfiguration Symfony.

**Configuration dans `services.yaml` (optionnel):**
```yaml
services:
    _defaults:
        autowire: true
        autoconfigure: true
    
    EnderLab\MarvinPluginMcpBundle\Mcp\Application\Tools\:
        resource: '../src/Mcp/Application/Tools/'
        tags: ['mcp.tool']
```

### 3. Configuration Claude Desktop

**macOS:** `~/Library/Application Support/Claude/claude_desktop_config.json`

**Windows:** `%APPDATA%\Claude\claude_desktop_config.json`

**Linux:** `~/.config/Claude/claude_desktop_config.json`

```json
{
  "mcpServers": {
    "marvin": {
      "command": "php",
      "args": [
        "/path/to/marvin-core/bin/console",
        "mcp:serve"
      ],
      "env": {
        "APP_ENV": "dev"
      }
    }
  }
}
```

---

## Patterns et Best Practices

### 1. Documentation Structurée

**Toujours retourner un array structuré:**
```php
public function __invoke(): array
{
    return [
        'data' => $actualData,        // Données principales
        'format' => 'json|yaml|text', // Format des données
        'purpose' => 'Description',   // Utilité pour le LLM
    ];
}
```

### 2. Exemples Concrets

**Inclure des exemples réels dans la documentation:**
```php
$description = <<<YAML
# Exemple 1: Simple
flow:
  name: "..."
  # ...

# Exemple 2: Complexe
flow:
  name: "..."
  conditions:
    # ...
YAML;
```

### 3. Nommage des Tools

**Convention:**
- Verbe à l'impératif : `describe-flow-yaml`, `list-devices`, `get-device-state`
- Séparé par des tirets : `kebab-case`
- Descriptif : le nom doit expliquer clairement l'action

### 4. Sécurité

**Validation des entrées:**
```php
#[McpTool(name: 'execute-flow')]
final readonly class ExecuteFlowTool
{
    public function __invoke(string $flowId): array
    {
        // Valider flowId
        Assert::uuid($flowId);
        
        // Vérifier permissions
        if (!$this->security->isGranted('FLOW_EXECUTE', $flowId)) {
            throw new AccessDeniedException();
        }
        
        // Exécuter
        // ...
    }
}
```

**Ne jamais exposer de données sensibles:**
- Pas de mots de passe
- Pas de tokens d'API
- Pas de données personnelles non autorisées

### 5. Performance

**Limiter la quantité de données retournées:**
```php
public function __invoke(int $limit = 100): array
{
    $devices = $this->deviceRepository->findAll();
    
    // Limiter les résultats
    $devices = array_slice($devices, 0, $limit);
    
    return [
        'devices' => $devices,
        'count' => count($devices),
        'limited' => true,
    ];
}
```

---

## Cas d'Usage

### 1. Génération de Flows

**Problème:** Les utilisateurs doivent connaître la syntaxe YAML complexe des flows.

**Solution:** Le LLM utilise `describe-flow-yaml` et génère le YAML à partir d'une description en langage naturel.

### 2. Assistance à la Configuration

**Problème:** Configuration d'un nouveau device avec toutes ses capacités.

**Solution:** Un tool `describe-device-capabilities` documente les capacités disponibles, le LLM génère la configuration.

### 3. Debugging de Flows

**Problème:** Un flow ne se déclenche pas comme prévu.

**Solution:** Un tool `analyze-flow` inspecte le flow et suggère des corrections basées sur la syntaxe et la logique.

### 4. Documentation Interactive

**Problème:** La documentation est statique et difficile à naviguer.

**Solution:** Le LLM accède aux tools MCP pour répondre à des questions spécifiques sur la syntaxe, les exemples, etc.

---

## Avantages du Bundle

### 1. Accessibilité

Les utilisateurs n'ont pas besoin de connaître la syntaxe YAML complexe, le LLM la génère pour eux.

### 2. Productivité

Création de flows 10x plus rapide via langage naturel qu'édition manuelle de YAML.

### 3. Qualité

Le LLM utilise la documentation officielle, garantissant une syntaxe correcte.

### 4. Découvrabilité

Les tools MCP rendent les fonctionnalités de Marvin découvrables par les LLMs.

### 5. Extensibilité

Facile d'ajouter de nouveaux tools pour exposer d'autres aspects de Marvin.

---

## Limitations Actuelles

### 1. Tools Limités

Actuellement, seul `describe-flow-yaml` est implémenté. D'autres tools sont nécessaires pour une expérience complète :
- `list-devices`
- `get-device-state`
- `describe-capabilities`
- `validate-flow-yaml`
- `execute-flow`

### 2. Pas de Prompts

Le bundle ne fournit pas encore de prompts MCP pour guider les LLMs.

### 3. Pas de Resources

Les resources MCP (accès à des URIs) ne sont pas encore implémentées.

### 4. Authentification

Pas de gestion d'authentification pour les tools (tous publics).

---

## Roadmap

### Version 1.1
- [ ] Tool `list-devices`
- [ ] Tool `get-device-state`
- [ ] Tool `describe-capabilities`
- [ ] Prompt `create-flow-guide`

### Version 1.2
- [ ] Tool `validate-flow-yaml`
- [ ] Tool `execute-flow`
- [ ] Tool `list-flows`
- [ ] Resource pour accès aux flows existants

### Version 2.0
- [ ] Authentification OAuth2 pour tools sensibles
- [ ] Rate limiting
- [ ] Audit logging des appels MCP
- [ ] Cache des réponses

---

## Dépendances

- PHP 8.4+
- Symfony 7.3+
- [MCP PHP SDK](https://github.com/modelcontextprotocol/php-sdk)

---

## Ressources

### Documentation externe
- [Model Context Protocol Specification](https://spec.modelcontextprotocol.io/)
- [MCP GitHub](https://github.com/modelcontextprotocol)
- [Claude MCP Integration](https://www.anthropic.com/mcp)

### Exemples de Tools MCP
- [MCP Servers Repository](https://github.com/modelcontextprotocol/servers)

---

**Dernière mise à jour:** 2025-10-24
**Auteur:** Documentation technique générée pour l'équipe de développement Marvin
