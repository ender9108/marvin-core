<?php

namespace Marvin\Security\Presentation\Api\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Marvin\Security\Domain\Exception\UserNotFound;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

final readonly class MeUserProvider implements ProviderInterface
{
    public function __construct(
        private Security $security,
        private UserRepositoryInterface $userRepository,
        private ObjectMapperInterface $objectMapper,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if (null === $this->security->getUser()) {
            return null;
        }

        $resourceClass = $operation->getClass();
        $user = $this->userRepository->find($this->security->getUser()->id);

        if (!$user instanceof User) {
            throw UserNotFound::withId($user->id);
        }

        return $this->objectMapper->map($user, $resourceClass);
    }
}
