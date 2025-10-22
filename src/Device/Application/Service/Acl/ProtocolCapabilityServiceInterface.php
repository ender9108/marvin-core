<?php

namespace Marvin\Device\Application\Service\Acl;

use Marvin\Device\Domain\ValueObject\NativeGroupInfo;
use Marvin\Device\Domain\ValueObject\NativeSceneInfo;
use Marvin\Shared\Domain\ValueObject\Identity\ProtocolId;

interface ProtocolCapabilityServiceInterface
{
    public function supportsNativeGroups(ProtocolId $protocolId): bool;

    public function supportsNativeScenes(ProtocolId $protocolId): bool;

    public function createNativeGroup(
        ProtocolId $protocolId,
        string $groupName,
        array $deviceAddresses
    ): ?NativeGroupInfo;

    public function createNativeScene(
        ProtocolId $protocolId,
        string $sceneName,
        array $deviceStates,
        ?string $groupName = null
    ): ?NativeSceneInfo;

    public function executeNativeGroupAction(
        NativeGroupInfo $groupInfo,
        string $action,
        array $params
    ): void;

    public function recallNativeScene(NativeSceneInfo $sceneInfo): void;

    public function deleteNativeGroup(NativeGroupInfo $groupInfo): void;

    public function deleteNativeScene(NativeSceneInfo $sceneInfo): void;
}
