<?php

namespace Marvin\Device\Application\QueryHandler\Group;

use EnderLab\DddCqrsBundle\Application\Query\QueryHandlerInterface;
use Marvin\Device\Application\Query\Group\GetGroupsCollection;
use Marvin\Device\Domain\Repository\DeviceRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetGroupsCollectionHandler implements QueryHandlerInterface
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository
    ) {}

    public function __invoke(GetGroupsCollection $query): array
    {
        return $this->deviceRepository->getGroups();
    }
}

