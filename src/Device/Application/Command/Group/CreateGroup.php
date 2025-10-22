<?php

namespace Marvin\Device\Application\Command\Group;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Device\Domain\ValueObject\CompositeStrategy;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Metadata;

final readonly class CreateGroup implements SyncCommandInterface
{
    public function __construct(
        public string $groupName,
        /** @var DeviceId[] $deviceIds */
        public array $deviceIds,
        public ?ZoneId $zoneId = null,
        public ?Metadata $metadata = null,
    ) {}
}
