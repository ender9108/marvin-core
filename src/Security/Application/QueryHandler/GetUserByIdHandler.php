<?php

namespace Marvin\Security\Application\QueryHandler;

use EnderLab\DddCqrsBundle\Application\Query\QueryHandlerInterface;
use Marvin\Security\Application\Query\GetUserById;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetUserByIdHandler implements QueryHandlerInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {
    }

    public function __invoke(GetUserById $query): User
    {
        return $this->userRepository->byId($query->id);
    }
}
