# DDD-CQRS-API-PLATFORM-BUNDLE - Technical Documentation

## Vue d'ensemble

Le **ddd-cqrs-api-platform-bundle** est une bibliothèque qui facilite l'intégration 
d'API Platform avec une architecture DDD/CQRS. Elle fournit des abstractions pour mapper 
automatiquement les entités de domaine vers des ressources API et vice-versa, 
tout en gérant les IRIs, et la pagination.

## Architecture

### Structure du bundle

```
src/
├── DddCqrsApiPlatformBundle.php    # Bundle principal
└── Infrastructure/
    └── Framework/
        └── ApiPlatform/
            ├── ApiResourceInterface.php          # Interface marker pour les ressources API
            ├── Mapper/
            │   ├── AbstractMapper.php           # Mapper de base avec traduction
            │   └── Attribute/
            │       └── AsTranslatableApiProperty.php  # Attribut pour propriétés traduisibles
            ├── State/
            │   ├── Provider/
            │   │   └── EntityToApiStateProvider.php   # Provider: Entity → API Resource
            │   └── Processor/
            │       └── ApiToEntityStateProcessor.php  # Processor: API Resource → Entity
            ├── Iri/
            │   ├── Iri.php                      # Value object IRI
            │   ├── IriConverter.php             # Convertisseur IRI
            │   └── IriService.php               # Service de serialization IRI
            └── Filter/
                └── PartialSearchFilter.php      # Filtre de recherche partielle
```

---

## Core Components

### 1. ApiResourceInterface

**Fichier:** `src/Infrastructure/Framework/ApiPlatform/ApiResourceInterface.php`

Interface marker pour les ressources API (DTOs exposés par API Platform).

```php
interface ApiResourceInterface {}
```

**Usage:**
```php
#[ApiResource]
final class UserApiResource implements ApiResourceInterface
{
    public ?string $id = null;
    public ?string $email = null;
    public ?string $firstname = null;
    public ?string $lastname = null;
}
```

**Rôle:**
- Marque les classes comme ressources API
- Distingue les DTOs des entités de domaine
- Facilite le mapping automatique

---

## Mapping Layer

### 1. AbstractMapper

**Fichier:** `src/Infrastructure/Framework/ApiPlatform/Mapper/AbstractMapper.php`

Classe de base pour les mappers avec support de la traduction automatique.

```php
abstract class AbstractMapper
{
    public function __construct(
        protected readonly TranslatorInterface $translator,
        protected readonly CacheInterface $cache,
        protected readonly ParameterBagInterface $parameters
    ) {}
    
    protected function translateDto(ApiResourceInterface $dto): ApiResourceInterface
    {
        // Traduction automatique des propriétés marquées
    }
    
    private function getTranslatableProperties(ApiResourceInterface $dto): array
    {
        // Récupération via reflection + cache
    }
}
```

**Fonctionnalités:**
- Traduction automatique des propriétés marquées avec `#[AsTranslatableApiProperty]`
- Cache des propriétés traduisibles pour performance
- Utilisation de Symfony Translator

**Pattern:**
```php
final class UserMapper extends AbstractMapper
{
    public function mapToApi(User $entity): UserApiResource
    {
        $dto = new UserApiResource();
        $dto->id = $entity->id->toString();
        $dto->email = $entity->email->value;
        
        // Traduction automatique si nécessaire
        return $this->translateDto($dto);
    }
}
```

---

### 2. AsTranslatableApiProperty

**Fichier:** `src/Infrastructure/Framework/ApiPlatform/Mapper/Attribute/AsTranslatableApiProperty.php`

Attribut PHP pour marquer les propriétés nécessitant une traduction.

```php
#[Attribute(Attribute::TARGET_PROPERTY)]
final class AsTranslatableApiProperty
{
    public const TYPE_TRANSLATION_FILE = 'translation_file';
    
    public function __construct(
        public readonly string $type = self::TYPE_TRANSLATION_FILE,
        public readonly string $domain = 'messages'
    ) {}
}
```

**Usage:**
```php
final class DeviceApiResource implements ApiResourceInterface
{
    public ?string $id = null;
    
    #[AsTranslatableApiProperty(domain: 'devices')]
    public ?string $status = null;  // Sera traduit automatiquement
    
    public ?string $name = null;
}
```

**Pattern:**
1. La propriété contient une clé de traduction (ex: `device.status.online`)
2. `AbstractMapper::translateDto()` détecte l'attribut via reflection
3. La valeur est traduite automatiquement via `TranslatorInterface`
4. Le cache évite la reflection répétée

---

## State Layer

### 1. EntityToApiStateProvider

**Fichier:** `src/Infrastructure/Framework/ApiPlatform/State/Provider/EntityToApiStateProvider.php`

State Provider qui transforme les entités Doctrine en ressources API.

```php
readonly class EntityToApiStateProvider implements ProviderInterface
{
    public function __construct(
        private ProviderInterface $collectionProvider,  // Doctrine CollectionProvider
        private ProviderInterface $itemProvider,        // Doctrine ItemProvider
        private ObjectMapperInterface $objectMapper,
    ) {}
    
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof CollectionOperationInterface) {
            // Collection: Entity[] → ApiResource[]
            $entities = $this->collectionProvider->provide($operation, $uriVariables, $context);
            $dtos = [];
            foreach ($entities as $entity) {
                $dtos[] = $this->objectMapper->map($entity, $resourceClass);
            }
            return new TraversablePaginator(...);
        }
        
        // Item: Entity → ApiResource
        $entity = $this->itemProvider->provide($operation, $uriVariables, $context);
        return $this->objectMapper->map($entity, $resourceClass);
    }
}
```

**Responsabilités:**
- Délègue à Doctrine pour récupérer les entités
- Mappe les entités vers les ressources API via `ObjectMapper`
- Gère la pagination pour les collections
- Préserve les métadonnées de pagination (page, items per page, total)

**Configuration:**
```php
#[ApiResource(
    provider: EntityToApiStateProvider::class
)]
final class UserApiResource implements ApiResourceInterface
{
    // ...
}
```

---

### 2. ApiToEntityStateProcessor

**Fichier:** `src/Infrastructure/Framework/ApiPlatform/State/Processor/ApiToEntityStateProcessor.php`

State Processor qui transforme les ressources API en entités et persiste via Doctrine.

```php
final readonly class ApiToEntityStateProcessor implements ProcessorInterface
{
    public function __construct(
        protected ProcessorInterface $persistProcessor,  // Doctrine PersistProcessor
        protected ProcessorInterface $removeProcessor,   // Doctrine RemoveProcessor
        private MicroMapperInterface $microMapper,
    ) {}
    
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ?ApiResourceInterface
    {
        $entityClass = $operation->getStateOptions()->getEntityClass();
        
        // Mapping ApiResource → Entity
        $entity = $this->microMapper->map($data, $entityClass);
        
        // Traitement selon l'opération
        switch (true) {
            case $operation instanceof Put:
            case $operation instanceof Patch:
                if (method_exists($entity, 'update')) {
                    $entity->update($context['previous_data']);
                }
                break;
            
            case $operation instanceof DeleteOperationInterface:
                if (method_exists($entity, 'delete')) {
                    $entity->delete();
                }
                $this->removeProcessor->process($entity, $operation, $uriVariables, $context);
                return null;
        }
        
        // Persistance via Doctrine
        $this->persistProcessor->process($entity, $operation, $uriVariables, $context);
        
        // Re-mapping Entity → ApiResource pour la réponse
        return $this->microMapper->map($entity, get_class($data));
    }
}
```

**Responsabilités:**
- Mappe les ressources API vers les entités de domaine
- Appelle les méthodes métier (`update()`, `delete()`) si elles existent
- Délègue la persistance à Doctrine
- Re-mappe l'entité vers la ressource API pour la réponse
- Gère PUT, PATCH, POST, DELETE

**Pattern d'intégration DDD:**
```php
// Entité de domaine
final class User extends AggregateRoot
{
    public function update(User $previous): void
    {
        // Logique métier pour mise à jour
        if ($this->email !== $previous->email) {
            $this->recordThat(new UserEmailChanged(...));
        }
    }
    
    public function delete(): void
    {
        $this->recordThat(new UserDeleted($this->id));
    }
}
```

**Configuration:**
```php
#[ApiResource(
    processor: ApiToEntityStateProcessor::class,
    stateOptions: new Options(entityClass: User::class)
)]
final class UserApiResource implements ApiResourceInterface
{
    // ...
}
```

---

## IRI Handling

### 1. Iri Value Object

**Fichier:** `src/Infrastructure/Framework/ApiPlatform/Iri/Iri.php`

Value object représentant un IRI (Internationalized Resource Identifier).

```php
final readonly class Iri
{
    public function __construct(
        public string $value
    ) {}
    
    public function __toString(): string
    {
        return $this->value;
    }
}
```

**Usage:**
```php
$iri = new Iri('/api/users/123e4567-e89b-12d3-a456-426614174000');
echo $iri;  // "/api/users/123e4567-e89b-12d3-a456-426614174000"
```

---

### 2. IriService

**Fichier:** `src/Infrastructure/Framework/ApiPlatform/Iri/IriService.php`

Service utilitaire pour la sérialisation/désérialisation des IRIs.

```php
class IriService
{
    public static function serializeIri(Iri $iri): string
    {
        return (string) $iri;
    }
    
    public static function deserializeIri(string $iri): ?Iri
    {
        if (empty($iri)) {
            return null;
        }
        return new Iri($iri);
    }
}
```

**Usage:**
```php
// Sérialisation
$iriString = IriService::serializeIri($iri);

// Désérialisation
$iri = IriService::deserializeIri('/api/users/123');
```

---

### 3. IriConverter

**Fichier:** `src/Infrastructure/Framework/ApiPlatform/Iri/IriConverter.php`

Convertisseur pour transformer les IRIs en entités et vice-versa.

**Usage typique:**
- Résolution d'IRIs vers entités pour relations
- Génération d'IRIs depuis entités pour réponses API

---

## Filters

### PartialSearchFilter

**Fichier:** `src/Infrastructure/Framework/ApiPlatform/Filter/PartialSearchFilter.php`

Filtre personnalisé pour la recherche partielle dans les collections.

**Configuration:**
```php
#[ApiResource]
#[ApiFilter(PartialSearchFilter::class, properties: ['name', 'email'])]
final class UserApiResource implements ApiResourceInterface
{
    // ...
}
```

**Usage API:**
```
GET /api/users?name=john
GET /api/users?email=example.com
```

---

## Patterns et Best Practices

### 1. Séparation Domain / API

**Architecture recommandée:**

```
Domain Layer (src/Domain/)
    └── User (entité de domaine)

API Layer (src/Presentation/Api/)
    └── UserApiResource (DTO API)
        
Mapping Layer
    └── UserToApiMapper (Domain → API)
    └── ApiToUserMapper (API → Domain)
```

**Avantages:**
- Le domaine reste isolé des concerns API
- Les DTOs peuvent avoir une structure différente
- Évolutivité sans casser le domaine

---

### 2. Workflow Complet

#### Lecture (GET)

```
1. Request: GET /api/users/123
   ↓
2. API Platform appelle EntityToApiStateProvider
   ↓
3. Provider récupère l'entité via Doctrine
   ↓
4. ObjectMapper mappe User → UserApiResource
   ↓
5. AbstractMapper traduit les propriétés marquées
   ↓
6. Response: UserApiResource JSON
```

#### Écriture (POST/PUT/PATCH)

```
1. Request: POST /api/users + JSON body
   ↓
2. API Platform désérialise en UserApiResource
   ↓
3. ApiToEntityStateProcessor mappe UserApiResource → User
   ↓
4. Appelle User::update() ou User::create() (logique métier)
   ↓
5. DomainEventDispatcher collecte les événements
   ↓
6. Doctrine persiste User
   ↓
7. Mapper re-mappe User → UserApiResource
   ↓
8. Response: UserApiResource JSON
```

---

### 3. Mapping avec ObjectMapper

**Configuration du Mapper:**
```php
namespace App\Presentation\Api\Mapper;

use Symfony\Component\ObjectMapper\Attribute\MapFrom;
use Marvin\Security\Domain\Model\User;
use App\Presentation\Api\Resource\UserApiResource;

final class UserToApiMapper
{
    #[MapFrom(User::class)]
    public function mapToApi(User $user): UserApiResource
    {
        $dto = new UserApiResource();
        $dto->id = $user->id->toString();
        $dto->email = $user->email->value;
        $dto->firstname = $user->firstname->value;
        $dto->lastname = $user->lastname->value;
        
        return $dto;
    }
}
```

**Mapping automatique:**
```php
// Symfony ObjectMapper détecte automatiquement les mappers
$apiResource = $this->objectMapper->map($user, UserApiResource::class);
```

---

### 4. Traduction des Propriétés

**Définition:**
```php
final class DeviceApiResource implements ApiResourceInterface
{
    public ?string $id = null;
    
    #[AsTranslatableApiProperty(domain: 'devices')]
    public ?string $statusLabel = null;
}
```

**Mapper:**
```php
final class DeviceMapper extends AbstractMapper
{
    public function mapToApi(Device $device): DeviceApiResource
    {
        $dto = new DeviceApiResource();
        $dto->id = $device->id->toString();
        
        // Clé de traduction (non traduite)
        $dto->statusLabel = 'device.status.' . $device->status->value;
        
        // Traduction automatique
        return $this->translateDto($dto);
    }
}
```

**Fichier de traduction (`translations/devices.fr.yaml`):**
```yaml
device:
    status:
        online: "En ligne"
        offline: "Hors ligne"
```

**Résultat JSON:**
```json
{
    "id": "123",
    "statusLabel": "En ligne"
}
```

---

### 5. Gestion des Relations avec IRI

**Ressource API:**
```php
final class DeviceApiResource implements ApiResourceInterface
{
    public ?string $id = null;
    public ?string $name = null;
    
    // Relation via IRI
    public ?string $zone = null;  // IRI: /api/zones/456
}
```

**Mapping depuis Entity:**
```php
public function mapToApi(Device $device): DeviceApiResource
{
    $dto = new DeviceApiResource();
    $dto->id = $device->id->toString();
    $dto->name = $device->name->value;
    
    // Génération IRI pour la relation
    if ($device->zone) {
        $dto->zone = '/api/zones/' . $device->zone->id->toString();
    }
    
    return $dto;
}
```

**Mapping vers Entity:**
```php
public function mapToEntity(DeviceApiResource $dto): Device
{
    // Résolution IRI → Entity
    $zone = null;
    if ($dto->zone) {
        $zoneId = $this->extractIdFromIri($dto->zone);
        $zone = $this->zoneRepository->byId(new ZoneId($zoneId));
    }
    
    return Device::create(
        new DeviceName($dto->name),
        $zone
    );
}
```

---

## Configuration

### 1. Services

```yaml
# config/services.yaml
services:
    # State Provider
    EnderLab\DddCqrsApiPlatformBundle\Infrastructure\Framework\ApiPlatform\State\Provider\EntityToApiStateProvider:
        autowire: true
        autoconfigure: true
    
    # State Processor
    EnderLab\DddCqrsApiPlatformBundle\Infrastructure\Framework\ApiPlatform\State\Processor\ApiToEntityStateProcessor:
        autowire: true
        autoconfigure: true
    
    # Mappers (étendre AbstractMapper)
    _instanceof:
        EnderLab\DddCqrsApiPlatformBundle\Infrastructure\Framework\ApiPlatform\Mapper\AbstractMapper:
            autowire: true
```

### 2. API Resource

```php
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Doctrine\Orm\State\Options;
use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\Framework\ApiPlatform\ApiResourceInterface;
use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\Framework\ApiPlatform\State\Provider\EntityToApiStateProvider;
use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\Framework\ApiPlatform\State\Processor\ApiToEntityStateProcessor;

#[ApiResource(
    shortName: 'User',
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Put(),
        new Patch(),
        new Delete(),
    ],
    provider: EntityToApiStateProvider::class,
    processor: ApiToEntityStateProcessor::class,
    stateOptions: new Options(entityClass: User::class)
)]
final class UserApiResource implements ApiResourceInterface
{
    public ?string $id = null;
    public ?string $email = null;
    public ?string $firstname = null;
    public ?string $lastname = null;
}
```

---

## Usage Examples

### Exemple Complet: Device API Resource

**1. Entité de Domaine:**
```php
namespace Marvin\Device\Domain\Model;

use EnderLab\DddCqrsBundle\Domain\Model\AggregateRoot;

final class Device extends AggregateRoot
{
    public readonly DeviceId $id;
    
    public function __construct(
        private(set) DeviceName $name,
        private(set) DeviceStatus $status,
        private(set) ?Zone $zone = null,
    ) {
        $this->id = new DeviceId();
    }
    
    public function update(Device $previous): void
    {
        if (!$this->name->equals($previous->name)) {
            $this->recordThat(new DeviceNameChanged($this->id, $this->name));
        }
    }
}
```

**2. Ressource API:**
```php
namespace App\Presentation\Api\Resource;

use ApiPlatform\Metadata\ApiResource;
use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\Framework\ApiPlatform\ApiResourceInterface;
use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\Framework\ApiPlatform\Mapper\Attribute\AsTranslatableApiProperty;

#[ApiResource(
    provider: EntityToApiStateProvider::class,
    processor: ApiToEntityStateProcessor::class,
    stateOptions: new Options(entityClass: Device::class)
)]
final class DeviceApiResource implements ApiResourceInterface
{
    public ?string $id = null;
    public ?string $name = null;
    
    #[AsTranslatableApiProperty(domain: 'devices')]
    public ?string $statusLabel = null;
    
    public ?string $zone = null;  // IRI
}
```

**3. Mapper:**
```php
namespace App\Presentation\Api\Mapper;

use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\Framework\ApiPlatform\Mapper\AbstractMapper;
use Symfony\Component\ObjectMapper\Attribute\MapFrom;

final class DeviceMapper extends AbstractMapper
{
    #[MapFrom(Device::class)]
    public function mapToApi(Device $device): DeviceApiResource
    {
        $dto = new DeviceApiResource();
        $dto->id = $device->id->toString();
        $dto->name = $device->name->value;
        $dto->statusLabel = 'device.status.' . $device->status->value;
        
        if ($device->zone) {
            $dto->zone = '/api/zones/' . $device->zone->id->toString();
        }
        
        return $this->translateDto($dto);
    }
}
```

**4. Réponse API:**
```json
{
    "@context": "/api/contexts/Device",
    "@id": "/api/devices/123",
    "@type": "Device",
    "id": "123",
    "name": "Lampe salon",
    "statusLabel": "En ligne",
    "zone": "/api/zones/456"
}
```

---

## Avantages du Bundle

### 1. Séparation des Concerns
- Le domaine ne connaît pas API Platform
- Les DTOs API sont indépendants des entités
- Facilite l'évolution sans casser l'API

### 2. Mapping Automatique
- ObjectMapper / MicroMapper gère le mapping
- Réduction du code boilerplate
- Type-safe avec PHP 8.4

### 3. Traduction Intégrée
- Traduction automatique via attribut
- Cache pour performance
- Support multilingue natif

### 4. Pagination Simplifiée
- Gestion automatique de la pagination
- Métadonnées préservées
- Compatible avec les filtres Doctrine

### 5. DDD-Friendly
- Appelle les méthodes métier (`update()`, `delete()`)
- Supporte les événements de domaine
- Intégration transparente avec ddd-cqrs-bundle

---

## Dépendances

- PHP 8.4+
- Symfony 7.3+
- API Platform 4.2+
- Doctrine ORM 3+
- symfony/object-mapper
- symfonycasts/micro-mapper

---

## Ressources

### Documentation externe
- [API Platform State](https://api-platform.com/docs/core/state-processors/)
- [Symfony Object Mapper](https://symfony.com/doc/current/components/object_mapper.html)
- [MicroMapper](https://github.com/SymfonyCasts/micro-mapper)

---

**Dernière mise à jour:** 2025-10-24
**Auteur:** Documentation technique générée pour l'équipe de développement Marvin
