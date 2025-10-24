# Security Bounded Context - Technical Documentation

## Vue d'ensemble

Le bounded context **Security** gère l'authentification, l'autorisation et la gestion des utilisateurs dans l'application Marvin. 
Il implémente une architecture DDD (Domain-Driven Design) avec CQRS et utilise JWT pour l'authentification stateless.

## Architecture

### Structure du bounded context

```
src/Security/
├── Application/
│   ├── Command/            # Commandes métier (write operations)
│   ├── CommandHandler/     # Handlers des commandes
│   ├── Query/              # Queries (read operations)
│   ├── QueryHandler/       # Handlers des queries
│   └── EventHandler/       # Handlers des événements de domaine
├── Domain/
│   ├── Model/              # Agrégats et entités
│   ├── ValueObject/        # Value Objects (Roles, UserStatus, etc.)
│   ├── Repository/         # Interfaces de repository
│   ├── Service/            # Services de domaine
│   ├── Event/              # Événements de domaine
│   ├── Exception/          # Exceptions métier
│   └── List/               # Enums et listes
├── Infrastructure/
│   ├── Framework/Symfony/
│   │   ├── Security/       # Intégration Symfony Security
│   │   ├── EventListener/  # Event listeners Symfony
│   │   ├── Service/        # Services infrastructure
│   │   └── Validator/      # Validateurs Symfony
│   └── Persistence/
│       └── Doctrine/       # Repositories Doctrine, Types DBAL
└── Presentation/
    └── Cli/                # Commandes CLI Symfony
```

---

## Domain Layer

### 1. Modèles de domaine

#### User (Aggregate Root)
**Fichier:** `src/Security/Domain/Model/User.php`

L'agrégat principal du bounded context. Représente un utilisateur du système.

**Propriétés:**
- `UserId $id` - Identité unique (auto-générée)
- `Email $email` - Email de l'utilisateur
- `Firstname $firstname` - Prénom
- `Lastname $lastname` - Nom
- `Roles $roles` - Rôles de l'utilisateur
- `Locale $locale` - Locale (fr, en, etc.)
- `Theme $theme` - Thème UI (dark, light)
- `UserStatus $status` - Statut du compte
- `UserType $type` - Type d'utilisateur (APP, CLI)
- `Timezone $timezone` - Fuseau horaire
- `?string $password` - Hash du mot de passe
- `?UpdatedAt $updatedAt` - Date de mise à jour
- `DateTimeInterface $createdAt` - Date de création

**Méthodes principales:**

```php
// Factory method
User::create(Email, Firstname, Lastname, UserStatus, UserType, Timezone, ...): User

// Gestion du profil
$user->changeEmail(Email $email): self
$user->updateProfile(?Firstname, ?Lastname, ?Roles, ?Theme, ?Locale, ?Timezone): self

// Gestion du statut
$user->enable(): self
$user->disable(): self
$user->lock(): self
$user->delete(): self  // Émet un événement UserDeleted

// Gestion du mot de passe
$user->definePassword(string $password, PasswordHasherInterface): self
$user->resetPassword(string $password, PasswordHasherInterface): self
$user->changePassword(string $current, string $new, PasswordHasherInterface): self
```

**Règles métier:**
- `changePassword()` vérifie le mot de passe actuel
- `changePassword()` empêche de réutiliser le même mot de passe
- `delete()` enregistre un événement de domaine `UserDeleted`

---

#### LoginAttempt
**Fichier:** `src/Security/Domain/Model/LoginAttempt.php`

Enregistre les tentatives de connexion échouées pour tracking et sécurité.

**Propriétés:**
- `LoginAttemptId $id` - Identité unique
- `User $user` - Utilisateur concerné
- `DateTimeInterface $createdAt` - Date de la tentative

**Usage:**
```php
$loginAttempt = LoginAttempt::create($user);
```

**Intégration:** Le listener `LoginFailureListener` crée automatiquement un `LoginAttempt` lors d'échecs de connexion.
Si plus de 3 connexions échouées alors le user passe au statut "LOCKED".

---

#### RequestResetPassword
**Fichier:** `src/Security/Domain/Model/RequestResetPassword.php`

Gère les demandes de réinitialisation de mot de passe.

**Propriétés:**
- `RequestResetPasswordId $id` - Identité unique
- `string $token` - Token de réinitialisation
- `User $user` - Utilisateur concerné
- `ExpiresAt $expiresAt` - Date d'expiration (par défaut: +1 jour)
- `bool $used` - Indicateur d'utilisation
- `DateTimeInterface $createdAt` - Date de création

**Méthodes:**
```php
$request->isUsed(): bool
$request->markAsUsed(): void
```

**Règles métier:**
- Expiration automatique après 1 jour
- Le token ne peut être utilisé qu'une seule fois

---

### 2. Value Objects

#### UserStatus
**Fichier:** `src/Security/Domain/ValueObject/UserStatus.php`

Représente l'état d'un compte utilisateur.

**États disponibles:**
- `DISABLED` (0) - Compte désactivé
- `ENABLED` (1) - Compte actif
- `LOCKED` (2) - Compte verrouillé (après tentatives échouées)
- `TO_DELETE` (9) - Marqué pour suppression

**API:**
```php
// Factory methods
UserStatus::enabled()
UserStatus::disabled()
UserStatus::locked()
UserStatus::toDelete()

// Vérifications
$status->isEnabled(): bool
$status->isDisabled(): bool
$status->isLocked(): bool
$status->isToDelete(): bool
```

---

#### Roles
**Fichier:** `src/Security/Domain/ValueObject/Roles.php`

Collection de rôles avec garantie d'inclusion de `ROLE_USER`.

**Factory methods:**
```php
Roles::user()        // [ROLE_USER]
Roles::admin()       // [ROLE_USER, ROLE_ADMIN]
Roles::superAdmin()  // [ROLE_USER, ROLE_SUPER_ADMIN]
Roles::fromArray(['ROLE_ADMIN'])  // Depuis un tableau de strings
```

**Méthodes:**
```php
$roles->contains(string $role): bool
$roles->isUser(): bool
$roles->isAdmin(): bool
$roles->isSuperAdmin(): bool
$roles->toArray(): array
```

**Règle métier:** Le rôle `ROLE_USER` est **toujours** inclus automatiquement.

---

#### Role (Enum)
**Fichier:** `src/Security/Domain/List/Role.php`

```php
enum Role: string {
    case USER = 'ROLE_USER';
    case ADMIN = 'ROLE_ADMIN';
    case SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
}
```

**Hiérarchie (définie dans `security.yaml`):**
```yaml
role_hierarchy:
    ROLE_SUPER_ADMIN: [ROLE_ADMIN, ROLE_ALLOWED_TO_SWITCH]
    ROLE_ADMIN: ROLE_USER
```

---

#### UserType
**Fichier:** `src/Security/Domain/ValueObject/UserType.php`

Distingue les types d'utilisateurs.

**Types:**
- `APP` (1) - Utilisateur applicatif (interface web/API)
- `CLI` (2) - Utilisateur CLI (commandes console)

---

### 3. Services de domaine

#### PasswordHasherInterface
**Fichier:** `src/Security/Domain/Service/PasswordHasherInterface.php`

Interface pour le hachage de mots de passe.

```php
interface PasswordHasherInterface {
    public function hash(User $user, string $plainPassword): string;
    public function verify(User $user, string $plainPassword): bool;
}
```

**Implémentation:** `UserPasswordHasher` (Infrastructure)

---

#### UniqueEmailVerifierInterface
**Fichier:** `src/Security/Domain/Service/UniqueEmailVerifierInterface.php`

Vérifie l'unicité d'un email dans le système.

```php
interface UniqueEmailVerifierInterface {
    public function verify(Email $email): void;
}
```

**Exception:** Lance `EmailAlreadyUsed` si l'email existe déjà.

---

#### LastUserAdminVerifierInterface
**Fichier:** `src/Security/Domain/Service/LastUserAdminVerifierInterface.php`

Empêche la suppression du dernier administrateur.

```php
interface LastUserAdminVerifierInterface {
    public function verify(User $user): void;
}
```

**Exception:** Lance `LastUserAdmin` si tentative de suppression du dernier admin.

---

### 4. Événements de domaine

Tous dans `src/Security/Domain/Event/User/`:

- `UserCreated` - Émis lors de la création d'un utilisateur
- `UserDeleted` - Émis lors de la suppression
- `UserEnabled` - Émis lors de l'activation
- `UserDisabled` - Émis lors de la désactivation
- `UserLocked` - Émis lors du verrouillage
- `UserEmailUpdated` - Émis lors du changement d'email

**Pattern:** Ces événements permettent la communication inter-bounded-contexts via le bus d'événements.

---

### 5. Exceptions métier

**Fichier:** `src/Security/Domain/Exception/`

| Exception | Description |
|-----------|-------------|
| `UserNotFound` | Utilisateur introuvable |
| `UserIsDeleted` | Utilisateur supprimé |
| `UserIsDisabled` | Compte désactivé |
| `UserIsLocaked` | Compte verrouillé |
| `InvalidCurrentPassword` | Mot de passe actuel incorrect |
| `InvalidSamePassword` | Nouveau mot de passe identique à l'ancien |
| `EmailAlreadyUsed` | Email déjà utilisé |
| `LastUserAdmin` | Dernière administrateur, suppression interdite |
| `RequestResetPasswordNotFound` | Demande de reset introuvable |
| `RequestResetPasswordExpired` | Token de reset expiré |
| `RequestResetPasswordAlreadyUsed` | Token déjà utilisé |
| `RequestResetPasswordAlreadyExists` | Demande déjà existante |

---

## Application Layer

### Commandes (Write Operations)

**Localisation:** `src/Security/Application/Command/User/`

| Commande | Handler | Description |
|----------|---------|-------------|
| `CreateUser` | `CreateUserHandler` | Crée un utilisateur |
| `UpdateProfileUser` | `UpdateUserProfileHandler` | Met à jour le profil |
| `ChangeEmailUser` | `ChangeEmailUserHandler` | Change l'email |
| `ChangePasswordUser` | `ChangePasswordUserHandler` | Change le mot de passe |
| `EnableUser` | `EnableUserHandler` | Active le compte |
| `DisableUser` | `DisableUserHandler` | Désactive le compte |
| `LockUser` | `LockUserHandler` | Verrouille le compte |
| `DeleteUser` | `DeleteUserHandler` | Supprime le compte |
| `RequestResetPasswordUser` | `RequestResetPasswordUserHandler` | Demande reset password |
| `ResetPasswordUser` | `ResetPasswordUserHandler` | Réinitialise le password |
| `UserLoginAttempt` | `UserLoginAttemptHandler` | Enregistre tentative échouée |

**Pattern:**
- Toutes les commandes sont **synchrones** (`SyncCommandInterface`)
- Les handlers retournent `void` ou l'entité créée/modifiée
- Validation métier dans le handler

**Exemple - CreateUserHandler:**
```php
public function __invoke(CreateUser $command): User
{
    // 1. Validation métier
    $this->uniqueEmailVerifier->verify($command->email);
    
    // 2. Création du modèle
    $user = User::create(
        $command->email,
        $command->firstname,
        $command->lastname,
        UserStatus::enabled(),
        $command->type,
        $command->timezone,
        $command->roles,
        $command->locale,
        $command->theme,
    );
    
    // 3. Opérations complémentaires
    $user->definePassword($command->password, $this->passwordHasher);
    
    // 4. Persistance
    $this->userRepository->save($user);
    
    return $user;
}
```

---

### Queries (Read Operations)

**Localisation:** `src/Security/Application/Query/`

| Query | Handler | Description |
|-------|---------|-------------|
| `GetUserById` | `GetUserByIdHandler` | Récupère un utilisateur par ID |
| `GetUsersCollection` | `GetUsersCollectionHandler` | Liste des utilisateurs |

**Pattern:**
- Toutes les queries sont **synchrones**
- Lecture seule (pas de modification d'état)
- Retournent des données (entités ou DTOs)

---

## Infrastructure Layer

### 1. Intégration Symfony Security

#### SecurityUser
**Fichier:** `src/Security/Infrastructure/Framework/Symfony/Security/SecurityUser.php`

Adaptateur entre le modèle de domaine `User` et l'interface `UserInterface` de Symfony Security.

**Implémente:**
- `UserInterface`
- `PasswordAuthenticatedUserInterface`
- `JWTUserInterface` (Lexik JWT)
- `EquatableInterface`

**Factory methods:**
```php
// Depuis un User du domaine
SecurityUser::create(User $user): SecurityUser

// Depuis un payload JWT
SecurityUser::createFromPayload(mixed $username, array $payload): SecurityUser
```

**Propriétés exposées:**
```php
public readonly UserId $id;
public readonly Email $email;
public readonly ?string $password;
public readonly array $roles;
public readonly int $status;
```

---

#### SecurityUserProvider
**Fichier:** `src/Security/Infrastructure/Framework/Symfony/Security/SecurityUserProvider.php`

Provider Symfony Security pour charger les utilisateurs.

**Responsabilités:**
- Charge un utilisateur par email (identifier)
- Convertit `User` (domaine) → `SecurityUser` (Symfony)
- Gère le refresh des tokens

**Configuration (`security.yaml`):**
```yaml
providers:
    app_user_provider:
        id: Marvin\Security\Infrastructure\Framework\Symfony\Security\SecurityUserProvider
```

---

#### UserChecker
**Fichier:** `src/Security/Infrastructure/Framework/Symfony/Security/UserChecker.php`

Vérifie l'état du compte avant authentification.

**Vérifications (pré-auth):**
- Compte supprimé → `UserIsDeleted`
- Compte désactivé → `UserIsDisabled`
- Compte verrouillé → `UserIsLocaked`

**Configuration (`security.yaml`):**
```yaml
firewalls:
    api_login:
        user_checker: Marvin\Security\Infrastructure\Framework\Symfony\Security\UserChecker
    api:
        user_checker: Marvin\Security\Infrastructure\Framework\Symfony\Security\UserChecker
```

---

#### UserPasswordHasher
**Fichier:** `src/Security/Infrastructure/Framework/Symfony/Security/UserPasswordHasher.php`

Implémentation de `PasswordHasherInterface` utilisant le système de Symfony.

```php
public function hash(User $user, string $plainPassword): string
{
    return $this->hasher->hashPassword(
        SecurityUser::create($user),
        $plainPassword
    );
}

public function verify(User $user, string $plainPassword): bool
{
    return $this->hasher->isPasswordValid(
        SecurityUser::create($user),
        $plainPassword
    );
}
```

**Configuration (`security.yaml`):**
```yaml
password_hashers:
    Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface: 'auto'
```

---

### 2. Event Listeners

#### JWTListener
**Fichier:** `src/Security/Infrastructure/Framework/Symfony/EventListener/JWTListener.php`

Enrichit le payload JWT avec des données utilisateur supplémentaires.

**Écoute:** `lexik_jwt_authentication.on_jwt_created`

**Données ajoutées au token:**
```php
$payload['firstname'] = $user->firstname->value;
$payload['lastname'] = $user->lastname->value;
$payload['status'] = $user->status->reference->value;
$payload['id'] = $user->id->toString();
$payload['ip'] = $request?->getClientIp();
```

**Usage:** Ces données sont ensuite disponibles dans `SecurityUser::createFromPayload()`.

---

#### LoginFailureListener
**Fichier:** `src/Security/Infrastructure/Framework/Symfony/EventListener/LoginFailureListener.php`

Enregistre les tentatives de connexion échouées.

**Écoute:** `LoginFailureEvent`

**Logique:**
```php
if ($user && $event->getException() instanceof BadCredentialsException) {
    $this->commandBus->handle(new UserLoginAttempt($user->id));
}
```

**Utilité:** Permet le tracking des tentatives d'intrusion, base pour un système de rate limiting ou de verrouillage automatique.

---

### 3. Configuration Symfony Security

**Fichier:** `config/packages/security.yaml`

#### Firewalls

```yaml
firewalls:
    # Endpoint de login (génération JWT)
    api_login:
        pattern: ^/api/login
        stateless: true
        json_login:
            check_path: /api/login_check
            success_handler: lexik_jwt_authentication.handler.authentication_success
            failure_handler: lexik_jwt_authentication.handler.authentication_failure
        user_checker: Marvin\Security\Infrastructure\Framework\Symfony\Security\UserChecker

    # Routes API protégées
    api:
        pattern: ^/api
        stateless: true
        entry_point: jwt
        jwt: ~
        refresh_jwt:
            check_path: /api/token/refresh
        logout:
            path: api_token_invalidate
        user_checker: Marvin\Security\Infrastructure\Framework\Symfony\Security\UserChecker
```

#### Access Control

```yaml
access_control:
    - { path: ^/api/login, roles: PUBLIC_ACCESS }
    - { path: ^/api/docs, roles: PUBLIC_ACCESS }
    - { path: ^/api/token/refresh, roles: PUBLIC_ACCESS }
    - { path: ^/api/password/(request|reset), roles: PUBLIC_ACCESS }
    - { path: ^/api, roles: IS_AUTHENTICATED_FULLY }
    - { path: ^/, roles: IS_AUTHENTICATED_FULLY }
```

**Routes publiques:**
- `/api/login` - Authentification
- `/api/docs` - Documentation API
- `/api/token/refresh` - Refresh du JWT
- `/api/password/request` et `/api/password/reset` - Reset password

**Routes protégées:**
- Tout le reste nécessite `IS_AUTHENTICATED_FULLY`

---

### 4. JWT Configuration

**Fichier:** `config/packages/lexik_jwt_authentication.yaml`

Utilise le bundle `lexik/jwt-authentication-bundle` pour l'authentification JWT.

**Refresh Tokens:** `config/packages/gesdinet_jwt_refresh_token.yaml`
- Permet de renouveler un token expiré sans re-login
- Endpoint: `/api/token/refresh`

---

### 5. Repositories

#### UserRepositoryInterface
**Fichier:** `src/Security/Domain/Repository/UserRepositoryInterface.php`

```php
interface UserRepositoryInterface {
    public function save(User $user): void;
    public function byId(UserId $id): ?User;
    public function byEmail(Email $email): ?User;
    public function byIdentifier(string $identifier): ?User;
    // ...
}
```

**Implémentation:** `UserOrmRepository` (Doctrine ORM)

---

## Presentation Layer

### Commandes CLI

**Localisation:** `src/Security/Presentation/Cli/`

| Commande | Signature | Description |
|----------|-----------|-------------|
| `CreateUserCommand` | `marvin:security:user:create` | Crée un utilisateur |
| `ListUsersCommand` | `marvin:security:user:list` | Liste les utilisateurs |
| `EnableUserCommand` | `marvin:security:user:enable` | Active un compte |
| `DisableUserCommand` | `marvin:security:user:disable` | Désactive un compte |
| `LockUserCommand` | `marvin:security:user:lock` | Verrouille un compte |
| `DeleteUserCommand` | `marvin:security:user:delete` | Supprime un compte |
| `ChangeEmailUserCommand` | `marvin:security:user:change-email` | Change l'email |
| `ChangePasswordUserCommand` | `marvin:security:user:change-password` | Change le mot de passe |
| `RequestResetPasswordUserCommand` | `marvin:security:user:request-reset-password` | Demande reset password |
| `ResetPasswordUserCommand` | `marvin:security:user:reset-password` | Réinitialise password |

**Pattern:**
- Les commandes CLI utilisent les **Command/Query Bus** pour déléguer la logique métier
- Elles ne contiennent **aucune logique métier**, seulement de la présentation

**Exemple:**
```php
$this->commandBus->handle(new CreateUser(
    new Email($email),
    new Firstname($firstname),
    new Lastname($lastname),
    // ...
));
```

---

## Flux d'authentification

### 1. Login avec JWT

```
1. Client → POST /api/login_check
   Body: { "username": "user@example.com", "password": "secret" }

2. Symfony Security:
   a. SecurityUserProvider::loadUserByIdentifier()
      → Charge User depuis DB
      → Convertit en SecurityUser
   
   b. UserChecker::checkPreAuth()
      → Vérifie status (enabled/disabled/locked)
   
   c. PasswordHasher::isPasswordValid()
      → Vérifie le mot de passe

3a. Succès:
    - JWTListener::onJWTCreated()
      → Enrichit payload (firstname, lastname, status, id, ip)
    - Génération JWT
    - Réponse: { "token": "...", "refresh_token": "..." }

3b. Échec:
    - LoginFailureListener
      → Dispatch UserLoginAttempt command
      → Enregistre LoginAttempt en DB
    - Réponse 401
```

---

### 2. Authentification sur routes protégées

```
1. Client → GET /api/resource
   Header: Authorization: Bearer <JWT>

2. Symfony Security:
   a. JWT décodé
   b. SecurityUser::createFromPayload()
      → Création SecurityUser depuis payload JWT
   c. User injecté dans le Security Context

3. Accès autorisé selon les rôles
```

---

### 3. Refresh Token

```
1. Client → POST /api/token/refresh
   Body: { "refresh_token": "..." }

2. Gesdinet JWT Refresh Bundle:
   - Vérifie le refresh token en DB
   - Génère un nouveau JWT
   
3. Réponse: { "token": "...", "refresh_token": "..." }
```

---

### 4. Reset Password

```
1. Demande de reset:
   Client → POST /api/password/request
   Body: { "email": "user@example.com" }
   
   → RequestResetPasswordUserHandler
     - Crée RequestResetPassword (token + expiration)
     - Envoie email (Email/RequestResetPasswordUser)

2. Reset effectif:
   Client → POST /api/password/reset
   Body: { "token": "...", "password": "new_password" }
   
   → ResetPasswordUserHandler
     - Vérifie token valide + non expiré + non utilisé
     - Change le mot de passe
     - Marque token comme utilisé
```

---

## Best Practices pour les développeurs

### 1. Création d'un nouvel utilisateur

**Dans l'application:**
```php
use Marvin\Security\Application\Command\User\CreateUser;

$command = new CreateUser(
    email: new Email('user@example.com'),
    firstname: new Firstname('John'),
    lastname: new Lastname('Doe'),
    type: new UserType('APP'),
    timezone: new Timezone('Europe/Paris'),
    password: 'plain_password',
    roles: Roles::user(),  // Par défaut
    locale: Locale::fr(),
    theme: Theme::dark()
);

$user = $this->commandBus->handle($command);
```

**En CLI:**
```bash
php bin/console marvin:security:user:create \
    user@example.com \
    --firstname=John \
    --lastname=Doe \
    --password=secret
```

---

### 2. Vérifier les permissions

**Dans un controller:**
```php
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SomeController extends AbstractController
{
    #[Route('/admin/resource')]
    public function adminAction(): Response
    {
        // Vérifier le rôle
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        // Ou
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createAccessDeniedException();
        }
        
        // Récupérer l'utilisateur actuel
        /** @var SecurityUser $securityUser */
        $securityUser = $this->getUser();
        $userId = $securityUser->id;
        
        // ...
    }
}
```

**Dans un service:**
```php
use Symfony\Bundle\SecurityBundle\Security;

class SomeService
{
    public function __construct(
        private Security $security
    ) {}
    
    public function doSomething(): void
    {
        $user = $this->security->getUser();
        
        if ($this->security->isGranted('ROLE_ADMIN')) {
            // ...
        }
    }
}
```

---

### 3. Gestion des erreurs

**Capturer les exceptions métier:**
```php
use Marvin\Security\Domain\Exception\EmailAlreadyUsed;
use Marvin\Security\Domain\Exception\UserNotFound;

try {
    $this->commandBus->handle(new CreateUser(...));
} catch (EmailAlreadyUsed $e) {
    // Email déjà utilisé
} catch (UserNotFound $e) {
    // Utilisateur introuvable
}
```

---

### 4. Écouter les événements de domaine

**Créer un EventHandler dans un autre bounded context:**

```php
namespace Marvin\Notification\Application\EventHandler;

use Marvin\Security\Domain\Event\User\UserCreated;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class SendWelcomeEmailOnUserCreated
{
    public function __invoke(UserCreated $event): void
    {
        // Envoyer un email de bienvenue
    }
}
```

**Pattern:** Les événements de domaine sont routés via Messenger et peuvent être écoutés par d'autres bounded contexts.

---

### 5. Tester l'authentification

**Dans les tests fonctionnels:**

```php
use App\Tests\Factory\Security\UserFactory;

class SomeControllerTest extends ApiTestCase
{
    public function testProtectedEndpoint(): void
    {
        // Créer un utilisateur de test
        $user = UserFactory::new()->admin()->create();
        
        // Authentifier
        $client = static::createAuthenticatedClient($user);
        
        // Requête
        $client->request('GET', '/api/resource');
        
        $this->assertResponseIsSuccessful();
    }
}
```

---

## Patterns et conventions

### 1. Immutabilité
- Les Value Objects sont **readonly**
- Les entités utilisent `private(set)` pour l'encapsulation

### 2. Named constructors
- `User::create()` plutôt que `new User()`
- `UserStatus::enabled()`, `Roles::admin()`

### 3. Services de domaine
- Interfaces dans `Domain/Service/`
- Implémentations dans `Infrastructure/Framework/Symfony/Service/`

### 4. Exceptions métier
- Toutes dans `Domain/Exception/`
- Nommées selon l'erreur métier (pas technique)

### 5. CQRS strict
- **Commands** = write, retournent `void` ou l'entité
- **Queries** = read, pas de modification d'état

---

## Sécurité et bonnes pratiques

### 1. Mots de passe
- ✅ Toujours hasher avec `PasswordHasherInterface`
- ✅ Ne jamais logger les mots de passe
- ✅ Vérifier l'ancien mot de passe avant changement

### 2. JWT
- ✅ Tokens stateless, pas de session serveur
- ✅ Refresh tokens stockés en DB
- ✅ Payload enrichi avec données utilisateur (évite requêtes DB)

### 3. Rate limiting
- ✅ `LoginAttempt` enregistre les échecs mais pas de rate limiting automatique et verrouillage automatique après X tentatives

### 4. User Checker
- ✅ Vérifie statut **avant** authentification
- ✅ Empêche connexion de comptes désactivés/verrouillés/supprimés

### 5. Validation
- ✅ Validation dans les handlers (via services de domaine)
- ✅ `UniqueEmailVerifier` empêche doublons
- ✅ `LastUserAdminVerifier` empêche suppression dernier admin

---

## Ressources complémentaires

### Configuration
- `config/packages/security.yaml` - Symfony Security
- `config/packages/lexik_jwt_authentication.yaml` - JWT
- `config/packages/gesdinet_jwt_refresh_token.yaml` - Refresh tokens

### Documentation externe
- [Symfony Security](https://symfony.com/doc/current/security.html)
- [Lexik JWT Bundle](https://github.com/lexik/LexikJWTAuthenticationBundle)

---

## Questions fréquentes

**Q: Comment ajouter un nouveau rôle ?**
1. Ajouter le case dans `src/Security/Domain/List/Role.php`
2. Ajouter factory method dans `Roles.php` si besoin
3. Mettre à jour `role_hierarchy` dans `security.yaml`

**Q: Comment verrouiller un compte après X tentatives ?**
- Actuellement: Automatique au bout de 3 tentatives.

**Q: Les événements de domaine sont-ils asynchrones ?**
- Par défaut, asynchrones
- Configurer routing Messenger pour les rendre async si besoin

---

## Maintenance et évolutions

### TODO / Améliorations possibles
- [ ] Ajouter 2FA (Two-Factor Authentication)
- [ ] Implémenter OAuth2 (Google, GitHub, etc.)
- [ ] Ajouter permissions granulaires (Voters)
- [ ] Logger les actions sensibles (changement email, password, etc.)
- [ ] Implémenter une politique d'expiration de mots de passe
- [ ] Ajouter historique des connexions
- [ ] Implémenter révocation manuelle de JWT

### Points de vigilance
- ⚠️ Migrations: Toujours vérifier les types DBAL custom (`UserIdType`, etc.)
- ⚠️ Cache: Invalidation automatique via `CacheInvalidationListener`
- ⚠️ Tests: Utiliser `UserFactory` pour créer des utilisateurs de test
- ⚠️ Performance: Éviter N+1 queries dans `GetUsersCollection`

---

**Dernière mise à jour:** 2025-10-24
**Auteur:** Documentation technique générée pour l'équipe de développement Marvin
