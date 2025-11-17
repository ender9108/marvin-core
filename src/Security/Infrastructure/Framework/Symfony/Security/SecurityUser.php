<?php

/**
 * Marvin Core - DDD-based home automation system
 *
 * @package   Marvin\Core
 * @author    Alexandre Berthelot <alexandreberthelot9108@gmail.com>
 * @copyright 2024-present Alexandre Berthelot
 * @license   AGPL-3.0 License
 * @link      https://github.com/ender9108/marvin-core
 */

declare(strict_types=1);

namespace Marvin\Security\Infrastructure\Framework\Symfony\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;
use Marvin\Security\Domain\Model\User;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @property string $id
 */
final readonly class SecurityUser implements JWTUserInterface, PasswordAuthenticatedUserInterface, EquatableInterface
{
    public function __construct(
        public string $id,
        public string $email,
        public ?string $password,
        public array $roles,
        public string $status,
        public string $type,
        public ?string $locale = null,
        public ?string $timezone = null,
        public ?string $theme = null,
        public ?string $firstname = null,
        public ?string $lastname = null,
    ) {
    }

    public static function create(User $user): self
    {
        return new self(
            $user->id->toString(),
            $user->email->value,
            (string) $user->password,
            $user->roles->toArray(),
            $user->status->value,
            $user->type->value,
            $user->locale->value,
            $user->timezone->value,
            $user->theme->value,
            $user->firstname->value,
            $user->lastname->value,
        );
    }

    public function isEqualTo(UserInterface $user): bool
    {
        return $this->getUserIdentifier() === $user->getUserIdentifier();
    }

    /**
     * @param mixed $username
     * @param array<string, string> $payload
     * @return JWTUserInterface|SecurityUser
     */
    public static function createFromPayload(
        mixed $username,
        array $payload
    ): JWTUserInterface|SecurityUser {
        return new self(
            $payload['id'],
            $payload['email'],
            null,
            $payload['roles'],
            $payload['status'],
            $payload['type'],
            $payload['locale'],
            $payload['timezone'],
            $payload['theme'],
            $payload['firstname'],
            $payload['lastname'],
        );
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getTimezone(): string
    {
        return $this->timezone;
    }

    public function getTheme(): string
    {
        return $this->theme;
    }

    public function getFullname(): string
    {
        return $this->firstname.' '.$this->lastname;
    }
}
