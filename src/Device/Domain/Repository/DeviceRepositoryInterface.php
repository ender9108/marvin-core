<?php

namespace Marvin\Device\Domain\Repository;

use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;

interface DeviceRepositoryInterface
{
    public function save();
    public function remove();
    public function byId(DeviceId $id);

    /**
     * Trouve tous les devices composites (groupes et scènes)
     */
    public function findComposites(): array;

    /**
     * Trouve tous les groupes
     */
    public function findGroups(): array;

    /**
     * Trouve toutes les scènes
     */
    public function findScenes(): array;

    /**
     * Trouve tous les composites qui contiennent un device spécifique
     */
    public function findCompositesByChildDevice(DeviceId $childDeviceId): array;

    /**
     * Trouve tous les composites avec support natif
     */
    public function findCompositesWithNativeSupport(): array;
}
