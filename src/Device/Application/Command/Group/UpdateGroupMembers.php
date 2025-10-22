<?php

namespace Marvin\Device\Application\Command\Group;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;

final readonly class UpdateGroupMembers implements SyncCommandInterface
{
    public function __construct(
        public DeviceId $groupId,
        /** array<int, DeviceId> */
        public array $deviceIdsToAdd = [],
        /** array<int, DeviceId> */
        public array $deviceIdsToRemove = []
    ) {}
}
