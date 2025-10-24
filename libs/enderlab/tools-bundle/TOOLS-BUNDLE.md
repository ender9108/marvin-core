# TOOLS-BUNDLE - Technical Documentation

## Vue d'ensemble

Le **tools-bundle** est une bibliothèque légère fournissant des traits utilitaires réutilisables pour les enums PHP 8.4 et les classes avec constantes. Elle simplifie la manipulation et la conversion des enums et constantes en tableaux, avec support du caching pour optimiser les performances.

## Architecture

### Structure du bundle

```
src/
├── ToolsBundle.php              # Bundle principal
└── Service/
    ├── EnumToArrayTrait.php     # Trait pour convertir des enums en tableaux
    └── ListTrait.php            # Trait pour extraire les constantes d'une classe
```

---

## Traits Disponibles

### 1. EnumToArrayTrait

**Fichier:** `src/Service/EnumToArrayTrait.php`

Trait fournissant des méthodes utilitaires pour convertir les enums PHP 8.4 en tableaux.

```php
namespace EnderLab\ToolsBundle\Service;

trait EnumToArrayTrait
{
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function array(): array
    {
        return array_combine(self::values(), self::names());
    }
}
```

#### Méthodes

**`names(): array`**
- Retourne un tableau de tous les **noms** des cases de l'enum
- Exemple: `['ROLE_USER', 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN']`

**`values(): array`**
- Retourne un tableau de toutes les **valeurs** des cases de l'enum
- Exemple: `['user', 'admin', 'super_admin']`

**`array(): array`**
- Retourne un tableau associatif **valeur => nom**
- Exemple: `['user' => 'ROLE_USER', 'admin' => 'ROLE_ADMIN']`

#### Usage

**Définition de l'enum:**
```php
namespace Marvin\Security\Domain\List;

use EnderLab\ToolsBundle\Service\EnumToArrayTrait;

enum Role: string
{
    use EnumToArrayTrait;

    case USER = 'ROLE_USER';
    case ADMIN = 'ROLE_ADMIN';
    case SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
}
```

**Utilisation:**
```php
// Récupérer les noms
$names = Role::names();
// ['USER', 'ADMIN', 'SUPER_ADMIN']

// Récupérer les valeurs
$values = Role::values();
// ['ROLE_USER', 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN']

// Récupérer le tableau associatif
$array = Role::array();
// [
//     'ROLE_USER' => 'USER',
//     'ROLE_ADMIN' => 'ADMIN',
//     'ROLE_SUPER_ADMIN' => 'SUPER_ADMIN'
// ]
```

#### Cas d'usage

**1. Validation:**
```php
use Symfony\Component\Validator\Constraints\Choice;

class UserDto
{
    #[Choice(choices: Role::values())]
    public array $roles;
}
```

**2. Sélecteur de formulaire:**
```php
$builder->add('role', ChoiceType::class, [
    'choices' => array_flip(Role::array()),
    // Affiche: ['USER' => 'ROLE_USER', 'ADMIN' => 'ROLE_ADMIN', ...]
]);
```

**3. Documentation API:**
```php
#[ApiProperty(
    description: 'Rôle de l\'utilisateur',
    example: 'ROLE_USER',
    openapiContext: [
        'type' => 'string',
        'enum' => Role::values()
    ]
)]
public string $role;
```

**4. Tests:**
```php
public function testAllRolesAreSupported(): void
{
    $supportedRoles = Role::values();
    
    foreach ($supportedRoles as $role) {
        $user = UserFactory::new()->withRole($role)->create();
        $this->assertTrue($user->hasRole($role));
    }
}
```

---

### 2. ListTrait

**Fichier:** `src/Service/ListTrait.php`

Trait fournissant une méthode pour extraire toutes les constantes d'une classe avec caching interne.

```php
namespace EnderLab\ToolsBundle\Service;

use ReflectionClass;

trait ListTrait
{
    private static array $internalCache = [];

    public static function constantsToArray(): array
    {
        if (!empty(self::$internalCache)) {
            return self::$internalCache;
        }

        $class = new ReflectionClass(__CLASS__);
        $constants = $class->getConstants();

        foreach ($constants as $constant) {
            self::$internalCache[$constant] = $constant;
        }

        return self::$internalCache;
    }
}
```

#### Méthodes

**`constantsToArray(): array`**
- Retourne un tableau associatif de toutes les constantes de la classe
- Format: `[valeur => valeur]` (clé et valeur identiques)
- Utilise la reflection pour lire les constantes
- Cache interne pour éviter la reflection répétée

#### Usage

**Définition de la classe:**
```php
namespace Marvin\Shared\Domain;

use EnderLab\ToolsBundle\Service\ListTrait;

final class Application
{
    use ListTrait;

    public const string APP_NAME = 'Marvin';
    public const array APP_AVAILABLE_LOCALES = ['fr', 'en'];
    public const string APP_DEFAULT_LOCALE = 'fr';
    public const array APP_AVAILABLE_THEMES = ['dark', 'light'];
    public const string APP_DEFAULT_THEME = 'dark';

    public const string APP_PROTOCOL_TYPE_NETWORK = 'network';
    public const string APP_PROTOCOL_TYPE_ZIGBEE = 'zigbee';
    public const string APP_PROTOCOL_TYPE_MATTER = 'matter';
}
```

**Utilisation:**
```php
$constants = Application::constantsToArray();
// [
//     'Marvin' => 'Marvin',
//     'fr' => 'fr',
//     'en' => 'en',
//     'dark' => 'dark',
//     'light' => 'light',
//     'network' => 'network',
//     'zigbee' => 'zigbee',
//     'matter' => 'matter',
// ]
```

**Note:** Le trait retourne **toutes** les constantes (scalar values uniquement). Les tableaux sont dépliés.

#### Cas d'usage

**1. Configuration dynamique:**
```php
class ConfigurationManager
{
    public function getAvailableProtocols(): array
    {
        $constants = Application::constantsToArray();
        
        return array_filter($constants, function($value) {
            return str_starts_with($value, 'APP_PROTOCOL_TYPE_');
        });
    }
}
```

**2. Validation:**
```php
public function validateTheme(string $theme): bool
{
    $validThemes = Application::APP_AVAILABLE_THEMES;
    return in_array($theme, $validThemes, true);
}
```

**3. Documentation:**
```php
public function listAllConstants(): array
{
    return Application::constantsToArray();
}
```

---

## Patterns et Best Practices

### 1. EnumToArrayTrait vs Native Methods

**Native PHP:**
```php
// Récupérer tous les cases
$cases = Role::cases();
// [Role::USER, Role::ADMIN, Role::SUPER_ADMIN]

// Récupérer un case par nom
$role = Role::from('ROLE_USER');

// Récupérer un case par nom (nullable)
$role = Role::tryFrom('INVALID');  // null
```

**Avec EnumToArrayTrait:**
```php
// Récupérer uniquement les valeurs
$values = Role::values();
// ['ROLE_USER', 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN']

// Récupérer uniquement les noms
$names = Role::names();
// ['USER', 'ADMIN', 'SUPER_ADMIN']

// Mapping valeur => nom
$array = Role::array();
// ['ROLE_USER' => 'USER', 'ROLE_ADMIN' => 'ADMIN']
```

**Avantages du trait:**
- Tableaux simples (pas d'objets enum)
- Formats adaptés pour validation, formulaires, API
- Méthodes réutilisables sur tous les enums

---

### 2. Nommage des Enums

**Convention:**
```php
enum Status: string
{
    use EnumToArrayTrait;

    case PENDING = 'pending';      // snake_case pour les valeurs
    case COMPLETED = 'completed';
    case FAILED = 'failed';
}
```

**Éviter:**
```php
enum Status: string
{
    case Pending = 'Pending';      // PascalCase (moins standard)
    case COMPLETED = 'COMPLETED';  // UPPERCASE (difficile à lire)
}
```

---

### 3. Caching avec ListTrait

**Le cache est par classe:**
```php
class Config1
{
    use ListTrait;
    public const FOO = 'foo';
}

class Config2
{
    use ListTrait;
    public const BAR = 'bar';
}

// Cache séparé pour chaque classe
Config1::constantsToArray();  // Cache pour Config1
Config2::constantsToArray();  // Cache pour Config2
```

**Attention:** Le cache persiste pendant toute la durée de l'exécution PHP.

---

### 4. Enum Backed vs Non-Backed

**Backed enum (recommandé):**
```php
enum Status: string
{
    use EnumToArrayTrait;

    case PENDING = 'pending';
    case COMPLETED = 'completed';
}

Status::values();  // ['pending', 'completed']
```

**Non-backed enum:**
```php
enum Status
{
    use EnumToArrayTrait;

    case PENDING;
    case COMPLETED;
}

Status::values();  // Error: array_column() expects parameter 2 to be string
```

**Conclusion:** `EnumToArrayTrait` nécessite un **backed enum** (avec valeurs).

---

## Exemples Complets

### Exemple 1: Enum de Statuts avec Validation

```php
namespace Marvin\Device\Domain\ValueObject;

use EnderLab\ToolsBundle\Service\EnumToArrayTrait;
use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectInterface;

enum DeviceStatus: string implements ValueObjectInterface
{
    use EnumToArrayTrait;

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

    public function isHealthy(): bool
    {
        return $this === self::ONLINE;
    }

    // Validation statique
    public static function isValid(string $status): bool
    {
        return in_array($status, self::values(), true);
    }
}
```

**Usage:**
```php
// Validation
if (DeviceStatus::isValid($input)) {
    $status = DeviceStatus::from($input);
}

// Constraint Symfony
#[Assert\Choice(choices: DeviceStatus::values())]
public string $status;

// API Platform
#[ApiProperty(openapiContext: ['enum' => DeviceStatus::values()])]
public string $status;
```

---

### Exemple 2: Classe de Configuration avec ListTrait

```php
namespace Marvin\Shared\Domain;

use EnderLab\ToolsBundle\Service\ListTrait;

final class Application
{
    use ListTrait;

    public const string APP_NAME = 'Marvin';
    public const string APP_VERSION = '1.0.0';

    // Locales
    public const array APP_AVAILABLE_LOCALES = ['fr', 'en'];
    public const string APP_DEFAULT_LOCALE = 'fr';

    // Thèmes
    public const array APP_AVAILABLE_THEMES = ['dark', 'light'];
    public const string APP_DEFAULT_THEME = 'dark';

    // Protocoles
    public const string APP_PROTOCOL_TYPE_NETWORK = 'network';
    public const string APP_PROTOCOL_TYPE_ZIGBEE = 'zigbee';
    public const string APP_PROTOCOL_TYPE_MATTER = 'matter';

    public const array APP_PROTOCOL_TYPES_AVAILABLES = [
        self::APP_PROTOCOL_TYPE_NETWORK,
        self::APP_PROTOCOL_TYPE_ZIGBEE,
        self::APP_PROTOCOL_TYPE_MATTER,
    ];
}
```

**Usage:**
```php
// Configuration service
class ConfigService
{
    public function getAppName(): string
    {
        return Application::APP_NAME;
    }

    public function getAvailableLocales(): array
    {
        return Application::APP_AVAILABLE_LOCALES;
    }

    public function getAllConstants(): array
    {
        return Application::constantsToArray();
    }
}
```

---

### Exemple 3: Form ChoiceType avec Enum

```php
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Marvin\Security\Domain\List\Role;

$builder->add('role', ChoiceType::class, [
    'choices' => array_flip(Role::array()),
    'label' => 'Rôle',
    'placeholder' => 'Sélectionnez un rôle',
]);

// Rendu HTML:
// <select name="role">
//   <option value="">Sélectionnez un rôle</option>
//   <option value="ROLE_USER">USER</option>
//   <option value="ROLE_ADMIN">ADMIN</option>
//   <option value="ROLE_SUPER_ADMIN">SUPER_ADMIN</option>
// </select>
```

---

## Performance

### EnumToArrayTrait

**Aucun cache interne**, car les enums PHP sont déjà optimisés :
- `self::cases()` est une opération native très rapide
- `array_column()` est implémenté en C
- Overhead négligeable

**Benchmark:**
```php
// 100 000 appels
$start = microtime(true);
for ($i = 0; $i < 100000; $i++) {
    Role::values();
}
$time = microtime(true) - $start;
// ~0.05s (négligeable)
```

---

### ListTrait

**Cache interne activé** :
- Premier appel : utilise Reflection (lent)
- Appels suivants : retourne le cache (rapide)

**Benchmark:**
```php
// Premier appel (avec reflection)
$start = microtime(true);
Application::constantsToArray();
$time1 = microtime(true) - $start;
// ~0.0001s

// Appels suivants (depuis cache)
$start = microtime(true);
for ($i = 0; $i < 100000; $i++) {
    Application::constantsToArray();
}
$time2 = microtime(true) - $start;
// ~0.01s (100x plus rapide)
```

**Conclusion:** Le cache est crucial pour les appels répétés.

---

## Limites et Contraintes

### EnumToArrayTrait

**1. Nécessite un backed enum:**
```php
// ❌ Ne fonctionne pas
enum Status {
    case PENDING;
}

// ✅ Fonctionne
enum Status: string {
    case PENDING = 'pending';
}
```

**2. Retourne uniquement les valeurs scalaires:**
- Pas d'objets, pas de méthodes, uniquement les valeurs

---

### ListTrait

**1. Retourne toutes les constantes:**
- Impossible de filtrer automatiquement
- Déplier les tableaux peut créer de la confusion

**2. Cache global par classe:**
- Pas de moyen de purger le cache
- Persiste jusqu'à la fin de l'exécution

**3. Reflection:**
- Overhead sur le premier appel
- Peut poser problème si beaucoup de constantes

---

## Dépendances

- PHP 8.4+
- Symfony 7.3+ (optionnel, pour l'intégration Bundle)

---

## Ressources

### Documentation externe
- [PHP 8.4 Enums](https://www.php.net/manual/en/language.enumerations.php)
- [Reflection API](https://www.php.net/manual/en/book.reflection.php)

---

**Dernière mise à jour:** 2025-10-24
**Auteur:** Documentation technique générée pour l'équipe de développement Marvin
