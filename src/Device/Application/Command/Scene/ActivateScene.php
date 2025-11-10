<?php

declare(strict_types=1);

namespace Marvin\Device\Application\Command\Scene;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;

/**
 * Command to activate a scene
 *
 * Restores all devices in the scene to their stored states
 */
final readonly class ActivateScene implements SyncCommandInterface
{
    public function __construct(
        public DeviceId $sceneId,
    ) {
    }
}
