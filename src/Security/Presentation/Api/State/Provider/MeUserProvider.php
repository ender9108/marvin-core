<?php

namespace Marvin\Security\Presentation\Api\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfonycasts\MicroMapper\MicroMapperInterface;

final readonly class MeUserProvider implements ProviderInterface
{
    public function __construct(
        private Security $security,
        private UserRepositoryInterface $userRepository,
        private MicroMapperInterface $microMapper,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if (null === $this->security->getUser()) {
            return null;
        }

        $resourceClass = $operation->getClass();
        $user = $this->userRepository->byId($this->security->getUser()->id);

        return $this->microMapper->map($user, $resourceClass);
    }
}
