<?php
namespace Marvin\Security\Infrastructure\Framework\Symfony\Security;

use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\ValueObject\Identity\UserId;
use Marvin\Shared\Domain\ValueObject\Email;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class SecurityUser implements JWTUserInterface, PasswordAuthenticatedUserInterface, EquatableInterface
{
    public function __construct(
        public UserId $id,
        public Email $email,
        public ?string $password,
        public array $roles,
        public string $status,
    ) {
    }

    public static function create(User $user): self
    {
        return new self(
            $user->id,
            $user->email,
            (string) $user->password,
            $user->roles->toArray(),
            $user->status->reference->value
        );
    }

    public function isEqualTo(UserInterface $user): bool
    {
        // TODO: Implement isEqualTo() method.
    }

    public static function createFromPayload($username, array $payload): JWTUserInterface|SecurityUser
    {
        return new self(
            new UserId($payload['id']),
            new Email($payload['email']),
            null,
            $payload['roles'],
            $payload['status'],
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
        /** @var non-empty-string $email */
        $email = $this->email->value;

        return $email;
    }
}
