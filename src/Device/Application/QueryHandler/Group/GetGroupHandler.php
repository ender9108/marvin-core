<?php

namespace Marvin\Device\Application\QueryHandler\Group;

use EnderLab\DddCqrsBundle\Application\Query\QueryHandlerInterface;
use Marvin\Device\Application\Query\Group\GetGroup;
use Marvin\Device\Domain\Model\Device;
use Marvin\Device\Domain\Repository\DeviceRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetGroupHandler implements QueryHandlerInterface
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository
    ) {}

    public function __invoke(GetGroup $query): Device
    {
        return $this->deviceRepository->getGroupById($query->groupId);
    }
}

