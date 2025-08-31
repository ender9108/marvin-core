<?php

namespace App\System\Infrastructure\ApiPlatform\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\System\Domain\Model\User;
use App\System\Domain\Repository\UserRepositoryInterface;
use EnderLab\DddCqrsBundle\Domain\Exception\MissingModelException;
use Exception;
use Symfony\Bundle\SecurityBundle\Security;
use Symfonycasts\MicroMapper\MicroMapperInterface;

final readonly class MeProvider implements ProviderInterface
{
    public function __construct(
        private Security $security,
        private MicroMapperInterface $microMapper,
        private UserRepositoryInterface $userRepository,
    ) {
    }

    /**
     * @throws Exception
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if (null === $this->security->getUser()) {
            return null;
        }

        $resourceClass = $operation->getClass();
        $user = $this->userRepository->find($this->security->getUser()->getId());

        if (!$user instanceof User) {
            throw new MissingModelException('User not found.');
        }

        return $this->microMapper->map($user, $resourceClass);
    }
}
