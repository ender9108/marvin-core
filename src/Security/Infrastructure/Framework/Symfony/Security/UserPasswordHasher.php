<?php
namespace Marvin\Security\Infrastructure\Framework\Symfony\Security;

use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\Service\PasswordHasherInterface;
use Override;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserPasswordHasher implements PasswordHasherInterface
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    #[Override]
    public function hash(User $user, string $password): string
    {
        $securityUser = SecurityUser::create($user);
        return $this->passwordHasher->hashPassword($securityUser, $password);
    }

    #[Override]
    public function verify(User $user, string $password): bool
    {
        $securityUser = SecurityUser::create($user);
        return $this->passwordHasher->isPasswordValid($securityUser, $password);
    }
}
