<?php

namespace Marvin\Device\Application\Command\Device;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;

final readonly class RemoveDeviceFromGroup implements SyncCommandInterface
{
    public function __construct(
        public DeviceId $groupId,
        public DeviceId $deviceId,
        public bool $deleteNativeGroupIfEmpty = true, // Supprimer le groupe natif si vide après retrait
    ) {
    }
}
