<?php

namespace App\System\Infrastructure\ApiPlatform\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\System\Domain\Repository\UserRepositoryInterface;
use Exception;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

final readonly class MeProvider implements ProviderInterface
{
    public function __construct(
        private Security $security,
        private ObjectMapperInterface $objectMapper,
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
        $user = $this->userRepository->findOneBy(['id' => $this->security->getUser()->getId()]);

        return $this->objectMapper->map($user, $resourceClass);
    }
}
