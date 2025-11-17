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

namespace Marvin\Device\Domain\Repository;

use Marvin\Device\Domain\Model\Device;
use Marvin\Device\Domain\ValueObject\Capability;
use Marvin\Device\Domain\ValueObject\CompositeType;
use Marvin\Device\Domain\ValueObject\DeviceType;
use Marvin\Device\Domain\ValueObject\PhysicalAddress;
use Marvin\Device\Domain\ValueObject\Protocol;
use Marvin\Device\Domain\ValueObject\TechnicalName;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;
use Marvin\Shared\Domain\ValueObject\Identity\ProtocolId;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;

/**
 * DeviceRepositoryInterface - Repository for Device Aggregate
 *
 * Provides persistence operations for Device aggregates including:
 * - Basic CRUD operations
 * - Queries by various criteria (protocol, zone, capability, type)
 * - Composite device queries (groups, scenes)
 * - Native group/scene lookups
 */
interface DeviceRepositoryInterface
{
    /**
     * Get all devices
     *
     * @return Device[]
     */
    public function all(): array;

    /**
     * Find a device by its ID
     */
    public function byId(DeviceId $deviceId): Device;

    /**
     * Find devices by protocol
     * If protocolId is provided, filter by specific protocol instance
     *
     * @return Device[]
     */
    public function byProtocol(Protocol $protocol, ?ProtocolId $protocolId = null): array;

    /**
     * Find devices in a specific zone
     *
     * @return Device[]
     */
    public function byZone(ZoneId $zoneId): array;

    /**
     * Find devices supporting a specific capability
     *
     * @return Device[]
     */
    public function byCapability(Capability $capability): array;

    /**
     * Find devices by type (ACTUATOR, SENSOR, COMPOSITE)
     *
     * @return Device[]
     */
    public function byType(DeviceType $deviceType): array;

    /**
     * Find composite devices (groups or scenes)
     * If compositeType is provided, filter by specific type
     *
     * @return Device[]
     */
    public function getCompositeDevices(?CompositeType $compositeType = null): array;

    /**
     * Find a physical device by its physical address and protocol
     * Useful for device discovery/pairing
     */
    public function byPhysicalAddress(PhysicalAddress $physicalAddress, Protocol $protocol): ?Device;

    /**
     * Find a device by its technical name and protocol ID
     * Used for Zigbee2MQTT friendly_name resolution
     */
    public function byTechnicalName(TechnicalName $technicalName, ProtocolId $protocolId): ?Device;

    /**
     * Find composite devices (groups/scenes) that contain a specific child device
     * Returns all groups/scenes that include the given device as a member
     *
     * @return Device[]
     */
    public function byChildDeviceId(DeviceId $childDeviceId): array;

    /**
     * Bulk fetch devices by their IDs
     * Optimized for loading child devices of composite devices
     *
     * @param DeviceId[] $deviceIds
     * @return Device[] Indexed by device ID string
     */
    public function byDevicesById(array $deviceIds): array;

    /**
     * Find a device by its native group ID
     * Used for protocol-native groups (Zigbee2MQTT, Hue, etc.)
     */
    public function byNativeGroupId(string $nativeGroupId, ProtocolId $protocolId): ?Device;

    /**
     * Find a device by its native scene ID
     * Used for protocol-native scenes (Zigbee2MQTT, Hue, etc.)
     */
    public function byNativeSceneId(string $nativeSceneId, ProtocolId $protocolId): ?Device;

    /**
     * Save a device (create or update)
     */
    public function save(Device $device): void;

    /**
     * Remove a device
     */
    public function remove(Device $device): void;

    /**
     * Check if a device exists by ID
     */
    public function exists(DeviceId $deviceId): bool;

    /**
     * Count devices by various criteria
     */
    public function countDevices(?Protocol $protocol = null, ?ZoneId $zoneId = null): int;
}
