<?php

namespace Marvin\Device\Application\Command\Scene;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;

final readonly class StoreSceneCurrentState implements SyncCommandInterface
{
    public function __construct(
        public DeviceId $sceneId
    ) {
    }
}
