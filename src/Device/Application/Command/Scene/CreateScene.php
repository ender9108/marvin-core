<?php

namespace Marvin\Device\Application\Command\Scene;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Device\Domain\ValueObject\CompositeStrategy;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Metadata;

final readonly class CreateScene implements SyncCommandInterface
{
    public function __construct(
        public Label $label,
        public array $sceneStates, // ['deviceId' => ['capability' => ['state' => value]]]
        public ?CompositeStrategy $strategy = null,
        public ?ZoneId $zoneId = null,
        public array $capabilities = [],
        public ?Metadata $metadata = null
    ) {}
}
