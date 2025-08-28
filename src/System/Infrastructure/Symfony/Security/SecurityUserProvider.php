<?php

namespace App\System\Infrastructure\Symfony\Security;

use App\System\Domain\Model\User;
use App\System\Domain\Model\UserStatus;
use App\System\Infrastructure\Doctrine\Repository\DoctrineUserRepository;
use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

readonly class SecurityUserProvider implements UserProviderInterface
{
    public function __construct(
        private DoctrineUserRepository $userRepository,
    ) {
    }

    /**
     * @throws Exception
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof SecurityUser) {
            throw new HttpException(401, 'Invalid credentials.');
        }

        /** @var User $userModel */
        $userModel = $this->userRepository->findOneBy([
            'id' => $user->getId(),
            'status' => UserStatus::STATUS_ENABLED
        ]);

        if (!$userModel) {
            throw new HttpException(401, 'Invalid credentials.');
        }

        return new SecurityUser($userModel);
    }

    public function supportsClass(string $class): bool
    {
        return $class === SecurityUser::class;
    }

    /**
     * @throws Exception
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $userEntity = $this->userRepository->byIdentifier($identifier);

        if (null === $userEntity) {
            throw new HttpException(401, 'Invalid credentials.');
        }

        return new SecurityUser($userEntity, []);
    }
}
