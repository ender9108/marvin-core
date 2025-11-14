<?php
/**
 * Marvin Core - DDD-based home automation system
 *
 * @package   Marvin\Core
 * @author    Alexandre Berthelot <alexandreberthelot9108@gmail.com>
 * @copyright 2024-present Alexandre Berthelot
 * @license   AGPL-3.0 License
 * @link      https://github.com/ender9108/marvin-core
 */
declare(strict_types=1);

namespace Marvin\Shared\Application\Service\Acl;

use Marvin\Shared\Application\Service\Acl\Dto\DeviceDto;
use RuntimeException;

/**
 * ACL Service : Protocol Context → Device Context
 *
 * Permet au Protocol Context d'interroger des informations sur les devices
 * depuis le Device Context sans dépendance directe
 */
interface DeviceQueryServiceInterface
{
    /**
     * Récupère les informations complètes d'un device
     *
     * @param string $deviceId UUID du device
     * @return DeviceDto
     * @throws RuntimeException Si device non trouvé
     */
    public function getDevice(string $deviceId): DeviceDto;

    /**
     * Récupère l'ID natif du device utilisé par le protocol
     *
     * L'ID natif est différent selon le protocol :
     * - Zigbee2MQTT : friendly_name (ex: "lamp_salon")
     * - Tasmota : topic (ex: "tasmota_ABC123")
     * - Shelly : IP address (ex: "192.168.1.100")
     * - Bluetooth : MAC address (ex: "AA:BB:CC:DD:EE:FF")
     *
     * @param string $deviceId UUID du device
     * @return string Native ID (friendly_name, MAC, IP, etc.)
     * @throws RuntimeException Si device non trouvé
     */
    public function getDeviceNativeId(string $deviceId): string;

    /**
     * Récupère le protocol logique du device
     *
     * @param string $deviceId UUID du device
     * @return string Protocol logique (zigbee, mqtt, wifi, bluetooth, etc.)
     * @throws RuntimeException Si device non trouvé
     */
    public function getDeviceProtocol(string $deviceId): string;

    /**
     * Récupère le topic MQTT du device (si applicable)
     *
     * Construit dynamiquement le topic basé sur le protocol et nativeId :
     * - Zigbee2MQTT : "zigbee2mqtt/{nativeId}"
     * - Tasmota : "stat/{nativeId}/RESULT"
     * - Shelly MQTT : "shellies/{nativeId}/relay/0"
     *
     * @param string $deviceId UUID du device
     * @return string|null Topic MQTT ou null si non applicable
     * @throws RuntimeException Si device non trouvé
     */
    public function getDeviceMqttTopic(string $deviceId): ?string;

    /**
     * Récupère l'URL REST du device (si applicable)
     *
     * Construit dynamiquement l'URL basée sur le protocol et nativeId :
     * - Shelly Gen1 : "http://{nativeId}/status"
     * - Shelly Gen2 : "http://{nativeId}/rpc"
     *
     * @param string $deviceId UUID du device
     * @return string|null URL REST ou null si non applicable
     * @throws RuntimeException Si device non trouvé
     */
    public function getDeviceRestUrl(string $deviceId): ?string;

    /**
     * Récupère les metadata du device
     *
     * Les metadata contiennent des informations comme :
     * - adapter : nom de l'adapter à utiliser
     * - model_id : identifiant modèle
     * - manufacturer : fabricant
     * - firmware_version : version firmware
     * - etc.
     *
     * @param string $deviceId UUID du device
     * @return array<string, mixed> Metadata du device
     * @throws RuntimeException Si device non trouvé
     */
    public function getDeviceMetadata(string $deviceId): array;
}
