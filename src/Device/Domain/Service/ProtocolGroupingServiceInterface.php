<?php

namespace Marvin\Device\Domain\Service;

use Marvin\Device\Domain\Model\Device;

interface ProtocolGroupingServiceInterface
{
    /**
     * Analyse les devices et détermine comment les regrouper
     *
     * @param Device[] $devices
     * @return array{
     *   native_groups: array<string, Device[]>,  // Protocol => [devices]
     *   individual_devices: Device[]
     * }
     */
    public function analyzeDevicesForGrouping(array $devices): array;

    /**
     * Vérifie si un protocole supporte les groupes natifs
     */
    public function supportsNativeGroups(string $protocol): bool;

    /**
     * Génère un nom de groupe natif pour un protocole
     */
    public function generateNativeGroupName(string $protocol, string $parentGroupName): string;

    /**
     * Génère un ID de groupe natif pour un protocole
     */
    public function generateNativeGroupId(string $protocol): string;
}
