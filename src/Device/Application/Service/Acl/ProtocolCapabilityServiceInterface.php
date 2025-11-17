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

namespace Marvin\Device\Application\Service\Acl;

use Marvin\Device\Domain\ValueObject\Capability;
use Marvin\Device\Domain\ValueObject\Protocol;

/**
 * ACL: Device → Protocol (capabilities)
 *
 * Service interface for querying protocol capabilities support from Device Context
 * Implemented in Infrastructure layer to avoid direct Protocol Context dependency
 */
interface ProtocolCapabilityServiceInterface
{
    /**
     * Get all capabilities supported by a protocol
     *
     * @return Capability[]
     */
    public function getSupportedCapabilities(Protocol $protocol): array;

    /**
     * Check if a protocol supports a specific capability
     */
    public function supportsCapability(Protocol $protocol, Capability $capability): bool;

    /**
     * Check if a protocol supports native groups
     */
    public function supportsNativeGroups(Protocol $protocol): bool;

    /**
     * Check if a protocol supports native scenes
     */
    public function supportsNativeScenes(Protocol $protocol): bool;

    /**
     * Execute an action on a device via the protocol
     *
     * @param array<string, mixed> $parameters Action parameters
     * @return array{success: bool, response?: mixed, error?: string}
     */
    public function executeAction(
        string $protocolId,
        string $nativeId,
        string $capability,
        string $action,
        array $parameters = [],
        int $timeout = 5000
    ): array;

    /**
     * Create a native group in the protocol
     *
     * Sends a command to the protocol adapter to create a native group
     * (e.g., Zigbee group, Matter group)
     *
     * @param string[] $deviceNativeIds Native IDs of devices to add to the group
     * @return array{success: bool, nativeGroupId?: string, friendlyName?: string, error?: string}
     */
    public function createNativeGroup(
        Protocol $protocol,
        string $protocolId,
        string $groupFriendlyName,
        array $deviceNativeIds
    ): array;

    /**
     * Create a native scene in the protocol
     *
     * Sends a command to the protocol adapter to create a native scene
     * (e.g., Zigbee scene, Matter scene)
     *
     * @param array<string, array<string, mixed>> $sceneStates Device states [deviceNativeId => [capability => value]]
     * @param string|null $groupId Optional native group ID to attach the scene to
     * @return array{success: bool, nativeSceneId?: string, friendlyName?: string, groupId?: string|null, error?: string}
     */
    public function createNativeScene(
        Protocol $protocol,
        string $protocolId,
        string $sceneFriendlyName,
        array $sceneStates,
        ?string $groupId = null
    ): array;

    /**
     * Update an existing native scene in the protocol
     *
     * Sends a command to the protocol adapter to update the states of an existing native scene
     * (e.g., Zigbee scene/store command)
     *
     * @param array<string, array<string, mixed>> $sceneStates Device states [deviceNativeId => [capability => value]]
     * @return array{success: bool, error?: string}
     */
    public function updateNativeScene(
        Protocol $protocol,
        string $protocolId,
        string $nativeSceneId,
        array $sceneStates
    ): array;
}
