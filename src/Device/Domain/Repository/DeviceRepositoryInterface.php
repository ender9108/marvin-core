<?php

namespace Marvin\Device\Domain\Repository;

use Marvin\Device\Domain\Model\Device;
use Marvin\Device\Domain\ValueObject\Identity\DeviceId;

interface DeviceRepositoryInterface
{
    public function save(Device $model): void;

    public function remove(Device $model): void;

    public function byId(DeviceId $id): ?Device;

    public function getComposites(): array;

    /**
     * Trouve tous les groupes
     */
    public function getGroups(): array;

    /**
     * Trouve toutes les scènes
     */
    public function getScenes(): array;

    /**
     * Trouve tous les composites qui contiennent un device spécifique
     */
    public function getCompositesByChildDevice(DeviceId $childDeviceId): array;

    /**
     * Trouve tous les composites avec support natif
     */
    public function getCompositesWithNativeSupport(): array;
}
