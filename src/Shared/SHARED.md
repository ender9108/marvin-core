# Bounded Context: Shared - Documentation Technique

## Vue d'ensemble

Le bounded context **Shared** contient les éléments partagés entre tous les bounded contexts de Marvin. Il regroupe les value objects communs, les interfaces de services transverses, les spécifications métier, les transformers API Platform, et les utilitaires génériques.

**Responsabilités principales :**
- Fournir des value objects réutilisables (Email, Label, Slug, Theme, Locale, etc.)
- Définir des interfaces de services transverses (Slugger, Mailer)
- Implémenter le pattern Specification pour les règles métier
- Gérer les exceptions et leur traduction
- Fournir des transformers pour API Platform
- Définir les constantes globales de l'application

---

## Table des matières

1. [Value Objects](#1-value-objects)
   - [Email](#email)
   - [Label](#label)
   - [Slug](#slug)
   - [Description](#description)
   - [Reference](#reference)
   - [Theme](#theme)
   - [Locale](#locale)
   - [ProtocolType](#protocoltype)
   - [Metadata](#metadata)
   - [Identity Value Objects](#identity-value-objects)
2. [Domain Services](#2-domain-services)
   - [SluggerInterface](#sluggerinterface)
   - [MailerInterface](#mailerinterface)
3. [Specifications](#3-specifications)
4. [Application Constants](#4-application-constants)
5. [Exception Handling](#5-exception-handling)
6. [Infrastructure](#6-infrastructure)
   - [Slugger Service](#slugger-service)
   - [Doctrine DBAL Types](#doctrine-dbal-types)
   - [API Platform Transformers](#api-platform-transformers)
7. [Patterns & Best Practices](#7-patterns--best-practices)

---

## 1. Value Objects

### Email

**Fichier:** `src/Shared/Domain/ValueObject/Email.php`

Représente une adresse email avec validation stricte.

**Validation:**
- Format email valide (RFC)
- Longueur entre 5 et 255 caractères
- Non vide

**API:**
```php
$email->value: string              // Valeur brute
$email->equals(Email $other): bool // Comparaison
$email->__toString(): string       // Cast en string
```

**Usage:**
```php
use Marvin\Shared\Domain\ValueObject\Email;

$email = new Email('user@example.com');

echo $email;           // "user@example.com"
echo $email->value;    // "user@example.com"

// Comparaison
if ($email->equals(new Email('user@example.com'))) {
    // Emails identiques
}

// Validation automatique (lance une exception si invalide)
$email = new Email('invalid-email'); // ❌ Assertion failed
```

**Cas d'usage:**
- Propriété d'un User
- Contact de protocole ou device
- Email de notification

---

### Label

**Fichier:** `src/Shared/Domain/ValueObject/Label.php`

Représente une étiquette textuelle (nom, libellé).

**Validation:**
- Longueur entre 2 et 255 caractères
- Non vide

**API:**
```php
Label::fromString(string $label): self
$label->value: string
$label->equals(Label $other): bool
$label->__toString(): string
```

**Usage:**
```php
use Marvin\Shared\Domain\ValueObject\Label;

$label = new Label('Salon');
// ou
$label = Label::fromString('Salon');

echo $label;        // "Salon"
echo $label->value; // "Salon"

// Comparaison
if ($label->equals(new Label('Salon'))) {
    // Labels identiques
}
```

**Cas d'usage:**
- Nom de zone (Zone)
- Label de device (Device)
- Nom de conteneur (Container)
- Nom de worker (Worker)

---

### Slug

**Fichier:** `src/Shared/Domain/ValueObject/Slug.php`

Représente un slug URL-friendly (sans validation stricte).

**API:**
```php
$slug->__toString(): string
```

**Usage:**
```php
use Marvin\Shared\Domain\ValueObject\Slug;

$slug = new Slug('salon-de-la-maison');

echo $slug; // "salon-de-la-maison"
```

**Note:** Ce value object est simple. Pour générer un slug depuis une string, utilisez le service `SluggerInterface`.

**Cas d'usage:**
- Slug de zone
- Slug de device
- URL-friendly identifiers

---

### Description

**Fichier:** `src/Shared/Domain/ValueObject/Description.php`

Représente une description textuelle longue.

**Validation:**
- Longueur entre 1 et 5000 caractères
- Non vide

**API:**
```php
$description->value: string
$description->equals(Description $other): bool
$description->__toString(): string
```

**Usage:**
```php
use Marvin\Shared\Domain\ValueObject\Description;

$description = new Description('Ceci est une description détaillée du dispositif.');

echo $description;        // "Ceci est une description..."
echo $description->value; // "Ceci est une description..."
```

**Cas d'usage:**
- Description de zone
- Description de device
- Notes détaillées

---

### Reference

**Fichier:** `src/Shared/Domain/ValueObject/Reference.php`

Représente une référence technique (code, identifiant court).

**Validation:**
- Longueur entre 3 et 64 caractères
- Non vide

**API:**
```php
$reference->value: string
$reference->equals(Reference $other): bool
$reference->__toString(): string
```

**Usage:**
```php
use Marvin\Shared\Domain\ValueObject\Reference;

$reference = new Reference('REF-2024-001');

echo $reference;        // "REF-2024-001"
echo $reference->value; // "REF-2024-001"
```

**Cas d'usage:**
- Référence de device
- Code de protocole
- Identifiant technique

---

### Theme

**Fichier:** `src/Shared/Domain/ValueObject/Theme.php`

Représente le thème de l'interface utilisateur.

**Validation:**
- Longueur max: 32 caractères
- Doit être dans `Application::APP_AVAILABLE_THEMES`

**Thèmes disponibles:**
- `dark` (par défaut)
- `light`

**API:**
```php
Theme::dark(): self
Theme::light(): self
$theme->value: string
$theme->equals(Theme $other): bool
$theme->__toString(): string
```

**Usage:**
```php
use Marvin\Shared\Domain\ValueObject\Theme;

// Factory methods
$theme = Theme::dark();
$theme = Theme::light();

// Constructeur avec validation
$theme = new Theme('dark');

echo $theme;        // "dark"
echo $theme->value; // "dark"
```

**Cas d'usage:**
- Préférence utilisateur (User)
- Configuration UI

---

### Locale

**Fichier:** `src/Shared/Domain/ValueObject/Locale.php`

Représente la locale (langue) de l'utilisateur.

**Validation:**
- Longueur: exactement 2 caractères
- Doit être dans `Application::APP_AVAILABLE_LOCALES`

**Locales disponibles:**
- `fr` (français, par défaut)
- `en` (anglais)

**API:**
```php
Locale::fr(): self
Locale::en(): self
$locale->value: string
$locale->equals(Locale $other): bool
$locale->__toString(): string
```

**Usage:**
```php
use Marvin\Shared\Domain\ValueObject\Locale;

// Factory methods
$locale = Locale::fr();
$locale = Locale::en();

// Constructeur avec validation
$locale = new Locale('fr');

echo $locale;        // "fr"
echo $locale->value; // "fr"
```

**Cas d'usage:**
- Préférence utilisateur (User)
- Traduction des messages
- Formatage des dates

---

### ProtocolType

**Fichier:** `src/Shared/Domain/ValueObject/ProtocolType.php`

Représente le type de protocole domotique.

**Validation:**
- Doit être dans `Application::APP_PROTOCOL_TYPES_AVAILABLES`

**Types disponibles:**
- `network` - Protocole réseau (HTTP, TCP, etc.)
- `zigbee` - Protocole Zigbee

**API:**
```php
ProtocolType::fromString(string $value): self
$protocolType->value: string
$protocolType->equals(ProtocolType $other): bool
$protocolType->__toString(): string
```

**Usage:**
```php
use Marvin\Shared\Domain\ValueObject\ProtocolType;

$type = new ProtocolType('zigbee');
// ou
$type = ProtocolType::fromString('zigbee');

echo $type;        // "zigbee"
echo $type->value; // "zigbee"
```

**Cas d'usage:**
- Type de protocole (Protocol)
- Configuration de device

---

### Metadata

**Fichier:** `src/Shared/Domain/ValueObject/Metadata.php`

Représente des métadonnées arbitraires sous forme de tableau associatif.

**API:**
```php
Metadata::fromArray(array $value): self
$metadata->value: array
$metadata->toArray(): array
$metadata->equals(Metadata $other): bool
```

**Usage:**
```php
use Marvin\Shared\Domain\ValueObject\Metadata;

$metadata = new Metadata([
    'manufacturer' => 'Philips',
    'model' => 'Hue Bridge',
    'version' => '2.0',
]);

// ou
$metadata = Metadata::fromArray([
    'manufacturer' => 'Philips',
    'model' => 'Hue Bridge',
]);

print_r($metadata->value);   // Array(...)
print_r($metadata->toArray()); // Array(...)
```

**Cas d'usage:**
- Métadonnées de device
- Métadonnées de container
- Métadonnées de worker
- Données supplémentaires flexibles

---

### Identity Value Objects

Les identity value objects représentent les identifiants uniques des entités.

**Fichiers:**
- `src/Shared/Domain/ValueObject/Identity/UserId.php`
- `src/Shared/Domain/ValueObject/Identity/DeviceId.php`
- `src/Shared/Domain/ValueObject/Identity/ZoneId.php`
- `src/Shared/Domain/ValueObject/Identity/ProtocolId.php`
- `src/Shared/Domain/ValueObject/Identity/UniqId.php`

#### UserId

```php
use Marvin\Shared\Domain\ValueObject\Identity\UserId;

$userId = new UserId();                    // Génère un nouvel UUID v7
$userId = new UserId('01234567-89ab-...');  // Depuis une string

echo $userId->toString();  // "01234567-89ab-cdef-0123-456789abcdef"
```

#### DeviceId

```php
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;

$deviceId = new DeviceId();
echo $deviceId->toString();
```

#### ZoneId

```php
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;

$zoneId = new ZoneId();
echo $zoneId->toString();
```

#### ProtocolId

```php
use Marvin\Shared\Domain\ValueObject\Identity\ProtocolId;

$protocolId = new ProtocolId();
echo $protocolId->toString();
```

#### UniqId

**Fichier:** `src/Shared/Domain/ValueObject/Identity/UniqId.php`

Étend `Symfony\Component\Uid\UuidV7` pour générer des UUIDs v7 (time-ordered).

```php
use Marvin\Shared\Domain\ValueObject\Identity\UniqId;

$uniqId = new UniqId();
echo $uniqId->toString();
```

**Cas d'usage:**
- Correlation IDs (ActionRequest)
- Tracking IDs
- Unique identifiers pour événements

---

## 2. Domain Services

### SluggerInterface

**Fichier:** `src/Shared/Domain/Service/SluggerInterface.php`

Interface pour générer des slugs URL-friendly.

**API:**
```php
interface SluggerInterface
{
    public function slugify(string $string): string;
}
```

**Usage:**
```php
use Marvin\Shared\Domain\Service\SluggerInterface;

class MyService
{
    public function __construct(
        private SluggerInterface $slugger
    ) {}
    
    public function createSlug(string $text): string
    {
        return $this->slugger->slugify($text);
    }
}

// Injection automatique via Symfony DI
$slug = $myService->createSlug('Salon de la Maison');
// Résultat: "salon-de-la-maison"
```

**Implémentation:**
Voir [Slugger Service](#slugger-service) dans la section Infrastructure.

---

### MailerInterface

**Fichier:** `src/Shared/Application/Email/MailerInterface.php`

Interface pour l'envoi d'emails.

**API:**
```php
interface MailerInterface
{
    public function send(EmailDefinitionInterface $email): void;
}
```

**Usage:**
```php
use Marvin\Shared\Application\Email\MailerInterface;
use Marvin\Shared\Application\Email\EmailDefinitionInterface;

class NotificationService
{
    public function __construct(
        private MailerInterface $mailer
    ) {}
    
    public function sendWelcomeEmail(User $user): void
    {
        $email = new WelcomeEmail($user); // implements EmailDefinitionInterface
        $this->mailer->send($email);
    }
}
```

**EmailDefinitionInterface:**
```php
interface EmailDefinitionInterface
{
    public function getTo(): array;
    public function getSubject(): string;
    public function getTemplate(): string;
    public function getContext(): array;
}
```

---

## 3. Specifications

**Fichier:** `src/Shared/Domain/Specification/SpecificationInterface.php`

Interface pour implémenter le pattern Specification.

**API:**
```php
interface SpecificationInterface
{
    public function isSatisfiedBy(mixed $value): bool;
}
```

**Usage:**

**1. Créer une spécification:**
```php
use Marvin\Shared\Domain\Specification\SpecificationInterface;

class DeviceIsOnlineSpecification implements SpecificationInterface
{
    public function isSatisfiedBy(mixed $device): bool
    {
        return $device->status->isOnline();
    }
}
```

**2. Utiliser la spécification:**
```php
$specification = new DeviceIsOnlineSpecification();

if ($specification->isSatisfiedBy($device)) {
    // Le device est en ligne
}
```

**3. Combiner des spécifications:**
```php
class AndSpecification implements SpecificationInterface
{
    public function __construct(
        private SpecificationInterface $left,
        private SpecificationInterface $right
    ) {}
    
    public function isSatisfiedBy(mixed $value): bool
    {
        return $this->left->isSatisfiedBy($value) 
            && $this->right->isSatisfiedBy($value);
    }
}

$isOnlineAndBatteryLow = new AndSpecification(
    new DeviceIsOnlineSpecification(),
    new BatteryLowSpecification()
);

if ($isOnlineAndBatteryLow->isSatisfiedBy($device)) {
    // Device en ligne ET batterie faible
}
```

**Exemple concret dans le projet:**
```php
// src/Device/Domain/Specification/CapabilityStateConstraints.php
use Marvin\Shared\Domain\Specification\SpecificationInterface;

class CapabilityStateConstraints implements SpecificationInterface
{
    public function isSatisfiedBy(mixed $value): bool
    {
        // Valider les contraintes sur l'état d'une capability
        return $this->validateConstraints($value);
    }
}
```

---

## 4. Application Constants

**Fichier:** `src/Shared/Domain/Application.php`

Classe contenant toutes les constantes globales de l'application.

**Constantes disponibles:**

### Application

```php
Application::APP_NAME              // "Marvin"
```

### Locales

```php
Application::APP_AVAILABLE_LOCALES // ['fr', 'en']
Application::APP_DEFAULT_LOCALE    // 'fr'
```

### Thèmes

```php
Application::APP_AVAILABLE_THEMES  // ['dark', 'light']
Application::APP_DEFAULT_THEME     // 'dark'
```

### Email

```php
Application::APP_EMAIL_FROM        // 'app-marvin@marvin.fr'
Application::APP_EMAIL_NAME        // 'Marvin'
```

### Protocoles

```php
Application::APP_PROTOCOL_TYPE_NETWORK  // 'network'
Application::APP_PROTOCOL_TYPE_ZIGBEE   // 'zigbee'
Application::APP_PROTOCOL_TYPE_MATTER   // 'matter'
Application::APP_PROTOCOL_TYPE_THREAD   // 'thread'
Application::APP_PROTOCOL_TYPE_ZWAVE    // 'zwave'

Application::APP_PROTOCOL_TYPES_AVAILABLES 
// ['network', 'zigbee']
```

### Weather Providers

```php
Application::APP_WEATHER_PRIVIDER_AVAILABLES 
// ['openweathermap', 'weatherapi', 'meteofrance']
```

**Usage:**
```php
use Marvin\Shared\Domain\Application;

// Vérifier si une locale est disponible
if (in_array($locale, Application::APP_AVAILABLE_LOCALES)) {
    // Locale valide
}

// Utiliser l'email par défaut
$from = Application::APP_EMAIL_FROM;

// Lister les protocoles disponibles
foreach (Application::APP_PROTOCOL_TYPES_AVAILABLES as $protocol) {
    echo $protocol;
}
```

---

## 5. Exception Handling

### ExceptionMessageManager

**Fichier:** `src/Shared/Presentation/Exception/Service/ExceptionMessageManager.php`

Service pour formater les exceptions en réponses standardisées (RFC 7807 Problem Details).

**API:**
```php
$manager->jsonResponseFormat(Throwable $exception): JsonResponse
$manager->cliResponseFormat(Throwable $exception): array
```

**Format de sortie:**
```json
{
    "type": "https://github.com/ender9108/marvin-core/blob/main/docs/error_code/error_code.md#user_not_found-E001",
    "title": "#user_not_found-E001",
    "detail": "L'utilisateur avec l'ID 123 n'a pas été trouvé",
    "debug": "Stack trace..." // En mode debug uniquement
}
```

**Usage dans un EventListener:**
```php
use Marvin\Shared\Presentation\Exception\Service\ExceptionMessageManager;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionListener
{
    public function __construct(
        private ExceptionMessageManager $exceptionMessageManager
    ) {}
    
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        
        $response = $this->exceptionMessageManager->jsonResponseFormat($exception);
        $event->setResponse($response);
    }
}
```

**Exceptions traduisibles:**

Pour qu'une exception soit correctement formatée, elle doit implémenter `TranslatableExceptionInterface` ou étendre `DomainException`.

**Exemple:**
```php
use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;

class UserNotFound extends DomainException
{
    public function __construct(UserId $userId)
    {
        parent::__construct(
            message: "User not found",
            translationId: 'security.user.user_not_found',
            internalCode: 'E001',
            translationParameters: ['id' => $userId->toString()]
        );
    }
}
```

**Traduction (translations/security.fr.yaml):**
```yaml
security:
  user:
    user_not_found: "L'utilisateur avec l'ID {id} n'a pas été trouvé"
```

---

### ExceptionListener

**Fichier:** `src/Shared/Presentation/Exception/EventListener/ExceptionListener.php`

EventListener Symfony qui capture toutes les exceptions et les formate avec `ExceptionMessageManager`.

**Configuration automatique:**
Cet EventListener est automatiquement enregistré via l'attribut `#[AsEventListener]`.

---

## 6. Infrastructure

### Slugger Service

**Fichier:** `src/Shared/Infrastructure/Framework/Symfony/Service/Slugger.php`

Implémentation de `SluggerInterface` utilisant le composant Symfony String.

**Implémentation:**
```php
namespace Marvin\Shared\Infrastructure\Framework\Symfony\Service;

use Marvin\Shared\Domain\Service\SluggerInterface;
use Symfony\Component\String\Slugger\SluggerInterface as SymfonySluggerInterface;

final readonly class Slugger implements SluggerInterface
{
    public function __construct(
        private SymfonySluggerInterface $slugger,
    ) {}
    
    public function slugify(string $string): string
    {
        return $this->slugger->slug($string);
    }
}
```

**Usage:**
```php
use Marvin\Shared\Domain\Service\SluggerInterface;

class ZoneService
{
    public function __construct(
        private SluggerInterface $slugger
    ) {}
    
    public function createZone(string $name): Zone
    {
        $slug = new Slug($this->slugger->slugify($name));
        
        return new Zone(
            label: new Label($name),
            slug: $slug,
        );
    }
}

// Injection automatique
$zone = $zoneService->createZone('Salon de la Maison');
// slug: "salon-de-la-maison"
```

---

### Doctrine DBAL Types

Les DBAL Types permettent de persister les value objects en base de données.

**Fichiers:**
- `src/Shared/Infrastructure/Persistence/Doctrine/DBAL/Types/EmailType.php`
- `src/Shared/Infrastructure/Persistence/Doctrine/DBAL/Types/UserIdType.php`
- `src/Shared/Infrastructure/Persistence/Doctrine/DBAL/Types/DeviceIdType.php`
- `src/Shared/Infrastructure/Persistence/Doctrine/DBAL/Types/ZoneIdType.php`
- `src/Shared/Infrastructure/Persistence/Doctrine/DBAL/Types/ProtocolIdType.php`
- `src/Shared/Infrastructure/Persistence/Doctrine/DBAL/Types/UniqIdType.php`

**Enregistrement (config/packages/doctrine.yaml):**
```yaml
doctrine:
    dbal:
        types:
            email: Marvin\Shared\Infrastructure\Persistence\Doctrine\DBAL\Types\EmailType
            user_id: Marvin\Shared\Infrastructure\Persistence\Doctrine\DBAL\Types\UserIdType
            device_id: Marvin\Shared\Infrastructure\Persistence\Doctrine\DBAL\Types\DeviceIdType
            zone_id: Marvin\Shared\Infrastructure\Persistence\Doctrine\DBAL\Types\ZoneIdType
            protocol_id: Marvin\Shared\Infrastructure\Persistence\Doctrine\DBAL\Types\ProtocolIdType
            uniq_id: Marvin\Shared\Infrastructure\Persistence\Doctrine\DBAL\Types\UniqIdType
```

**Mapping XML:**
```xml
<entity name="Marvin\Security\Domain\Model\User">
    <id name="id" type="user_id" column="id"/>
    <field name="email" type="email" column="email" unique="true"/>
</entity>
```

---

### API Platform Transformers

Les transformers permettent de convertir automatiquement les value objects dans les réponses API Platform.

**Fichiers:**
- `src/Shared/Infrastructure/Framework/Symfony/MapperTransformer/DatetimeValueObjectTransformer.php`
- `src/Shared/Infrastructure/Framework/Symfony/MapperTransformer/SubResourceTransformer.php`
- `src/Shared/Infrastructure/Framework/Symfony/MapperTransformer/SubCollectionResourceTransformer.php`

#### DatetimeValueObjectTransformer

Transforme les value objects datetime en string ISO 8601.

**Usage automatique:**
```php
// Votre entité
class Zone
{
    public UpdatedAt $updatedAt;
    public DateTimeInterface $createdAt;
}

// Réponse API automatique
{
    "updatedAt": "2024-10-24T12:00:00+00:00",
    "createdAt": "2024-10-24T10:00:00+00:00"
}
```

#### SubResourceTransformer

Transforme une sous-ressource en IRI.

#### SubCollectionResourceTransformer

Transforme une collection de sous-ressources en IRIs.

---

### UpdatedAt Doctrine Listener

**Fichier:** `src/Shared/Infrastructure/Persistence/Doctrine/ORM/Listener/UpdatedAtListener.php`

Listener Doctrine qui met automatiquement à jour le champ `updatedAt` lors de la modification d'une entité.

**Configuration automatique:**
Toute entité avec une propriété `updatedAt` de type `UpdatedAt` sera automatiquement mise à jour.

---

## 7. Patterns & Best Practices

### Utilisation des Value Objects

**Pattern recommandé:**

✅ **DO:**
```php
class Zone
{
    public function __construct(
        private Label $label,
        private Slug $slug,
        private ?Description $description = null,
    ) {}
}
```

❌ **DON'T:**
```php
class Zone
{
    public function __construct(
        private string $label,
        private string $slug,
        private ?string $description = null,
    ) {}
}
```

**Avantages:**
- Validation automatique à la construction
- Type-safety
- Encapsulation de la logique métier
- Réutilisabilité

---

### Value Objects immuables

Tous les value objects sont **readonly** et **immuables**.

✅ **DO:**
```php
final readonly class Email implements Stringable
{
    public string $value;
    
    public function __construct(string $email)
    {
        Assert::email($email);
        $this->value = $email;
    }
}
```

❌ **DON'T:**
```php
class Email
{
    public function __construct(
        public string $value
    ) {}
    
    public function setValue(string $value): void // ❌ Mutation interdite
    {
        $this->value = $value;
    }
}
```

---

### Factory Methods

Privilégier les factory methods pour les cas d'usage fréquents.

```php
final readonly class Theme implements Stringable
{
    public static function dark(): self
    {
        return new self('dark');
    }
    
    public static function light(): self
    {
        return new self('light');
    }
}

// Usage
$theme = Theme::dark();
```

---

### Pattern Specification

Utiliser le pattern Specification pour encapsuler les règles métier complexes.

**Avantages:**
- Règles métier réutilisables
- Testables unitairement
- Combinables (AND, OR, NOT)
- Lisibilité du code

**Exemple:**
```php
class CanRestartContainerSpecification implements SpecificationInterface
{
    public function isSatisfiedBy(mixed $container): bool
    {
        return $container->status->isRunning() 
            && $container->isActionAllowed('restart');
    }
}

// Usage
$spec = new CanRestartContainerSpecification();

if ($spec->isSatisfiedBy($container)) {
    $container->restart();
}
```

---

### Service Interfaces dans le Domain

Toujours définir les interfaces de services dans le **Domain** layer, et les implémenter dans l'**Infrastructure** layer.

**Domain:**
```php
// src/Shared/Domain/Service/SluggerInterface.php
namespace Marvin\Shared\Domain\Service;

interface SluggerInterface
{
    public function slugify(string $string): string;
}
```

**Infrastructure:**
```php
// src/Shared/Infrastructure/Framework/Symfony/Service/Slugger.php
namespace Marvin\Shared\Infrastructure\Framework\Symfony\Service;

use Marvin\Shared\Domain\Service\SluggerInterface;

class Slugger implements SluggerInterface
{
    // Implémentation
}
```

**Avantages:**
- Le domaine ne dépend pas de l'infrastructure
- Facilite les tests (mocking)
- Respect du principe d'inversion de dépendance (DIP)

---

### Exceptions du domaine

Toujours créer des exceptions spécifiques héritant de `DomainException`.

**Pattern recommandé:**
```php
use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;

class DeviceNotFound extends DomainException
{
    public function __construct(DeviceId $deviceId)
    {
        parent::__construct(
            message: "Device not found",
            translationId: 'device.device_not_found',
            internalCode: 'E001',
            translationParameters: ['id' => $deviceId->toString()]
        );
    }
}
```

**Fichier de traduction (translations/device.fr.yaml):**
```yaml
device:
  device_not_found: "Le device avec l'ID {id} n'a pas été trouvé"
```

**Usage:**
```php
$device = $repository->byId($deviceId);

if (!$device) {
    throw new DeviceNotFound($deviceId);
}
```

---

### Identity Value Objects

**Pattern recommandé:**

Toujours étendre `Symfony\Component\Uid\UuidV7` pour les identités.

```php
use Symfony\Component\Uid\UuidV7;

final class DeviceId extends UuidV7
{
}
```

**Usage:**
```php
// Génération automatique
$deviceId = new DeviceId();

// Depuis une string
$deviceId = new DeviceId('01234567-89ab-cdef-0123-456789abcdef');

// Conversion en string
echo $deviceId->toString();
```

**Avantages:**
- UUIDs v7 sont time-ordered (meilleur pour les index DB)
- Génération automatique
- Compatibilité avec Doctrine

---

### ArrayValueObjectInterface

Pour les value objects représentant des collections, implémenter `ArrayValueObjectInterface`.

**Interface:**
```php
interface ArrayValueObjectInterface
{
    public function toArray(): array;
}
```

**Exemple:**
```php
final readonly class Metadata
{
    public function __construct(
        public array $value = []
    ) {}
    
    public function toArray(): array
    {
        return $this->value;
    }
    
    public static function fromArray(array $value): self
    {
        return new self($value);
    }
}
```

---

### Tests avec Value Objects

**Créer des value objects en tests:**
```php
use Marvin\Shared\Domain\ValueObject\Email;
use Marvin\Shared\Domain\ValueObject\Label;

// Création directe
$email = new Email('test@example.com');
$label = new Label('Test Label');

// Avec Foundry (dans les factories)
UserFactory::createOne([
    'email' => new Email('user@example.com'),
    'firstname' => new Firstname('John'),
]);
```

---

## Résumé

Le bounded context **Shared** offre :

- **9 Value Objects principaux** : Email, Label, Slug, Description, Reference, Theme, Locale, ProtocolType, Metadata
- **5 Identity Value Objects** : UserId, DeviceId, ZoneId, ProtocolId, UniqId
- **2 Domain Services** : SluggerInterface, MailerInterface
- **Pattern Specification** pour les règles métier
- **Application Constants** centralisées
- **Exception Handling** avec RFC 7807 Problem Details
- **Infrastructure** : Slugger, DBAL Types, API Platform Transformers
- **UpdatedAt Listener** automatique

Le contexte Shared est le socle commun de tous les bounded contexts, fournissant les briques de base réutilisables et garantissant la cohérence du code.
