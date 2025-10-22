<?php

namespace Marvin\Device\Infrastructure\Framework\Symfony\Service\Protocol;

use Marvin\Device\Domain\Model\Device;
use Marvin\Device\Domain\Service\ProtocolGroupingServiceInterface;

final class ProtocolGroupingService implements ProtocolGroupingServiceInterface
{
    private const array NATIVE_GROUP_PROTOCOLS = [
        'zigbee' => true,
        'zwave' => true,
        'matter' => true,
        'thread' => true,
        'network' => false,
    ];

    private const int MIN_DEVICES_FOR_NATIVE_GROUP = 2;

    public function analyzeDevicesForGrouping(array $devices): array
    {
        $byProtocol = $this->groupDevicesByProtocol($devices);

        $nativeGroups = [];
        $individualDevices = [];

        foreach ($byProtocol as $protocol => $protocolDevices) {
            if ($this->shouldCreateNativeGroup($protocol, $protocolDevices)) {
                $nativeGroups[$protocol] = $protocolDevices;
            } else {
                // Ajouter individuellement
                $individualDevices = array_merge($individualDevices, $protocolDevices);
            }
        }

        return [
            'native_groups' => $nativeGroups,
            'individual_devices' => $individualDevices,
        ];
    }

    public function supportsNativeGroups(string $protocol): bool
    {
        return self::NATIVE_GROUP_PROTOCOLS[$protocol] ?? false;
    }

    public function generateNativeGroupName(string $protocol, string $parentGroupName): string
    {
        $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', $parentGroupName));
        return sprintf('groupe_%s_%s', $slug, $protocol);
    }

    public function generateNativeGroupId(string $protocol): string
    {
        return match ($protocol) {
            'zigbee' => (string) random_int(1000, 9999),
            'zwave' => (string) random_int(100, 999),
            'matter' => uniqid('matter_'),
            default => uniqid("{$protocol}_"),
        };
    }

    /**
     * Groupe les devices par protocole
     *
     * @param Device[] $devices
     * @return array<string, Device[]>
     */
    private function groupDevicesByProtocol(array $devices): array
    {
        $grouped = [];

        foreach ($devices as $device) {
            $protocol = $this->detectProtocol($device);

            if (!isset($grouped[$protocol])) {
                $grouped[$protocol] = [];
            }

            $grouped[$protocol][] = $device;
        }

        return $grouped;
    }

    /**
     * Détecte le protocole d'un device
     */
    private function detectProtocol(Device $device): string
    {
        // Récupérer depuis metadata
        $metadata = $device->metadata->toArray();

        if (isset($metadata['protocol'])) {
            return strtolower($metadata['protocol']);
        }

        // Fallback: essayer de détecter via d'autres indices
        // Ex: si reference commence par "0x" → probablement Zigbee
        if ($device->getReference()) {
            $ref = $device->getReference()->toString();
            if (str_starts_with($ref, '0x')) {
                return 'zigbee';
            }
            if (preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $ref)) {
                return 'wifi';
            }
        }

        return 'unknown';
    }

    /**
     * Détermine si on doit créer un groupe natif
     */
    private function shouldCreateNativeGroup(string $protocol, array $devices): bool
    {
        return $this->supportsNativeGroups($protocol) && count($devices) >= self::MIN_DEVICES_FOR_NATIVE_GROUP;
    }
}

