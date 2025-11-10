<?php

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
}
