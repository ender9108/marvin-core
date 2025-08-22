<?php

namespace App\System\Infrastructure\Symfony\Security;

use App\System\Domain\Model\User;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class SecurityUser implements JWTUserInterface, PasswordAuthenticatedUserInterface
{
    private string $id;

    private string $email;

    private string $firstName;

    private string $lastName;

    private array $roles = [];

    private ?string $password = null;

    /**
     * @throws Exception
     */
    public function __construct(
        ?User $user = null,
        array $payload = []
    ) {
        if (null !== $user) {
            $this->buildFromUserEntity($user, $payload);
        }
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @throws Exception
     */
    public static function createFromPayload($username, array $payload): JWTUserInterface|User
    {
        $user = new User();
        $user
            ->setFirstName($payload['firstName'])
            ->setLastName($payload['lastName'])
            ->setEmail($payload['email'])
            ->setRoles($payload['roles'])
        ;

        return new self(
            $user,
            $payload
        );
    }

    public function getRoles(): array
    {
        $roles = $this->roles ?? [];
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function eraseCredentials(): void
    {
        $this->password = null;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    private function buildFromUserEntity(User $user, array $payload): void
    {
        $this->id = $user->getId();
        $this->email = $user->getEmail();
        $this->firstName = $user->getFirstName();
        $this->lastName = $user->getLastName();
        $this->roles = $user->getRoles();
        $this->password = $user->getPassword();
    }
}
