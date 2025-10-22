<?php

namespace Marvin\Device\Domain\Model;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use EnderLab\DddCqrsBundle\Domain\Model\AggregateRoot;
use Marvin\Device\Domain\Event\Device\DeviceActionExecuted;
use Marvin\Device\Domain\Event\Device\DeviceAssignedToZone;
use Marvin\Device\Domain\Event\Device\DeviceCreated;
use Marvin\Device\Domain\Event\Device\DeviceDeleted;
use Marvin\Device\Domain\Event\Device\DeviceOffline;
use Marvin\Device\Domain\Event\Device\DeviceOnline;
use Marvin\Device\Domain\Event\Device\DeviceStateChanged;
use Marvin\Device\Domain\ValueObject\CompositeStrategy;
use Marvin\Device\Domain\ValueObject\CompositeType;
use Marvin\Device\Domain\ValueObject\DeviceStatus;
use Marvin\Device\Domain\ValueObject\DeviceType;
use Marvin\Device\Domain\ValueObject\NativeGroupInfo;
use Marvin\Device\Domain\ValueObject\NativeSceneInfo;
use Marvin\Device\Domain\ValueObject\VirtualDeviceType;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;
use Marvin\Shared\Domain\ValueObject\Identity\ProtocolId;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Metadata;


class Device extends AggregateRoot
{
    public readonly DeviceId $id;

    /**
     * @var Collection<int, DeviceCapability>
     */
    private(set) Collection $capabilities;

    /**
     * @var Collection<int, DeviceState>
     */
    private(set) Collection $states;

    public function __construct(
        private(set) Label $label,
        private(set) DeviceType $type,
        private(set) DeviceStatus $status,
        private(set) ?ProtocolId $protocolId = null,
        private(set) ?string $physicalAddress = null,
        private(set) ?VirtualDeviceType $virtualType = null,
        private(set) array $virtualConfig = [],
        /** array<int, DeviceId> */
        private(set) array $childDeviceIds = [],
        private(set) ?NativeGroupInfo $nativeGroupInfo = null,
        private(set) ?NativeSceneInfo $nativeSceneInfo = null,
        private(set) ?array $sceneStates = null, // Pour les scènes : états par device
        private(set) ?CompositeStrategy $compositeStrategy = null,
        private(set) ?ZoneId $zoneId = null,
        private(set) ?string $manufacturer = null,
        private(set) ?string $model = null,
        private(set) ?string $firmwareVersion = null,
        private(set) ?Metadata $metadata = null,
        private(set) ?DateTimeInterface $updatedAt = null,
        private(set) DateTimeInterface $createdAt = new DateTimeImmutable(),
    ) {
        $this->id = new DeviceId();
        $this->capabilities = new ArrayCollection();
        $this->states = new ArrayCollection();
    }

    // ============= Factory Methods =============

    public static function createPhysical(
        Label $label,
        ProtocolId $protocolId,
        string $physicalAddress,
        ?string $manufacturer = null,
        ?string $model = null
    ): self {
        $device = new self(
            label: $label,
            type: DeviceType::PHYSICAL,
            status: DeviceStatus::OFFLINE,
            protocolId: $protocolId,
            physicalAddress: $physicalAddress,
            manufacturer: $manufacturer,
            model: $model,
        );

        $device->recordThat(new DeviceCreated(
            deviceId: $device->id->toString(),
            label: $device->label->value,
            type: $device->type->value,
            protocolId: $device->protocolId->toString()
        ));

        return $device;
    }

    public static function createVirtual(
        Label $label,
        VirtualDeviceType $virtualType,
        array $virtualConfig
    ): self {
        $device = new self(
            label: $label,
            type: DeviceType::VIRTUAL,
            status: DeviceStatus::ONLINE,
            virtualType: $virtualType,
            virtualConfig: $virtualConfig,
        );

        $device->recordThat(new DeviceCreated(
            deviceId: $device->id->toString(),
            label: $device->label->value,
            type: $device->type->value,
            virtualType: $device->virtualType->value
        ));

        return $device;
    }

    public static function createGroup(
        Label $label,
        array $childDeviceIds,
        CompositeStrategy $strategy = CompositeStrategy::NATIVE_IF_AVAILABLE
    ): self {
        $device = new self(
            $label,
            DeviceType::COMPOSITE,
            DeviceStatus::ONLINE
        );
        $device->childDeviceIds = $childDeviceIds;
        $device->compositeStrategy = $strategy;

        $device->recordThat(new DeviceCreated(
            deviceId: $device->id->toString(),
            label: $label->value,
            type: DeviceType::COMPOSITE->value,
            compositeType: CompositeType::GROUP->value,
            childCount: count($childDeviceIds)
        ));

        return $device;
    }

    public static function createScene(
        Label $label,
        array $sceneStates,
        CompositeStrategy $strategy = CompositeStrategy::NATIVE_IF_AVAILABLE
    ): self {
        $device = new self(
            $label,
            DeviceType::COMPOSITE,
            DeviceStatus::ONLINE
        );

        // Extract childDeviceIds from sceneStates
        $device->childDeviceIds = array_map(
            fn($deviceId) => new DeviceId($deviceId),
            array_keys($sceneStates)
        );

        $device->sceneStates = $sceneStates;
        $device->compositeStrategy = $strategy;

        $device->recordThat(new DeviceCreated(
            deviceId: $device->id->toString(),
            label: $label->value,
            type: DeviceType::COMPOSITE->value,
            compositeType: CompositeType::SCENE->value,
            childCount: count($device->childDeviceIds)
        ));

        return $device;
    }

    public function delete(): void
    {
        $this->recordThat(new DeviceDeleted(
            deviceId: $this->id->toString(),
            name: $this->label->value,
        ));
    }

    public function addCapability(DeviceCapability $capability): void
    {
        if (!$this->capabilities->contains($capability)) {
            $this->capabilities->add($capability);
        }
    }

    public function updateState(string $capabilityName, mixed $value): void
    {
        $state = $this->getOrCreateState($capabilityName);
        $oldValue = $state->value;

        $state->updateValue($value);

        $this->recordThat(new DeviceStateChanged(
            deviceId: $this->id->toString(),
            capabilityName: $capabilityName,
            oldValue: $oldValue,
            newValue: $value
        ));
    }

    public function executeAction(string $capabilityName, string $action, array $params = []): void
    {
        $capability = $this->findCapability($capabilityName);

        if (!$capability) {
            /** @todo */
            //throw new DomainException("Capability {$capabilityName} not found on device {$this->label}");
        }

        if (!$capability->supportsAction($action)) {
            /** @todo */
            //throw new DomainException("Action {$action} not supported by capability {$capabilityName}");
        }

        $this->recordThat(new DeviceActionExecuted(
            deviceId: $this->id->toString(),
            capabilityName: $capabilityName,
            action: $action,
            params: $params
        ));
    }

    public function markOnline(): void
    {
        if ($this->status !== DeviceStatus::ONLINE) {
            $this->status = DeviceStatus::ONLINE;

            $this->recordThat(new DeviceOnline(
                deviceId: $this->id->toString(),
                label: $this->label
            ));
        }
    }

    public function markOffline(): void
    {
        if ($this->status !== DeviceStatus::OFFLINE) {
            $this->status = DeviceStatus::OFFLINE;

            $this->recordThat(new DeviceOffline(
                deviceId: $this->id->toString(),
                label: $this->label->value
            ));
        }
    }

    public function markUnavailable(string $reason): void
    {
        $this->status = DeviceStatus::UNAVAILABLE;
        $this->metadata = new Metadata([
            'unavailable_reason' => $reason,
        ]);
    }

    public function assignToZone(ZoneId $zoneId): void
    {
        $oldZoneId = $this->zoneId?->toString();
        $this->zoneId = $zoneId;

        $this->recordThat(new DeviceAssignedToZone(
            deviceId: $this->id->toString(),
            zoneId: $zoneId->toString(),
            previousZoneId: $oldZoneId
        ));
    }

    public function unassignFromZone(): void
    {
        if ($this->zoneId !== null) {
            $oldZoneId = $this->zoneId->toString();
            $this->zoneId = null;

            $this->recordThat(new DeviceAssignedToZone(
                deviceId: $this->id->toString(),
                zoneId: null,
                previousZoneId: $oldZoneId
            ));
        }
    }

    public function updateFirmware(string $version): void
    {
        $this->firmwareVersion = $version;
    }

    private function getOrCreateState(string $capabilityName): DeviceState
    {
        foreach ($this->states as $state) {
            if ($state->getCapabilityName() === $capabilityName) {
                return $state;
            }
        }

        $newState = new DeviceState($capabilityName);
        $this->states->add($newState);
        return $newState;
    }

    private function findCapability(string $name): ?DeviceCapability
    {
        foreach ($this->capabilities as $capability) {
            if ($capability->getName() === $name) {
                return $capability;
            }
        }

        return null;
    }

    public function getState(string $capabilityName): ?DeviceState
    {
        foreach ($this->states as $state) {
            if ($state->getCapabilityName() === $capabilityName) {
                return $state;
            }
        }
        return null;
    }

    public function addChildDevice(DeviceId $deviceId): void
    {
        if (!$this->isComposite()) {
            throw new \DomainException("Only composite devices can have child devices");
        }

        if (!$this->hasChildDevice($deviceId)) {
            $this->childDeviceIds[] = $deviceId;
        }
    }

    public function removeChildDevice(DeviceId $deviceId): void
    {
        if (!$this->isComposite()) {
            /** @todo */
            //throw new \DomainException("Only composite devices can have child devices");
        }

        $this->childDeviceIds = array_filter(
            $this->childDeviceIds,
            fn($id) => !$id->equals($deviceId)
        );
    }

    public function hasChildDevice(DeviceId $deviceId): bool
    {
        return array_any($this->childDeviceIds, fn($childId) => $childId->equals($deviceId));
    }

    public function setNativeGroupInfo(NativeGroupInfo $info): void
    {
        if (!$this->isComposite()) {
            /** @todo */
            //throw new \DomainException("Only composite devices can have native group info");
        }

        $this->nativeGroupInfo = $info;
    }

    public function setNativeSceneInfo(NativeSceneInfo $info): void
    {
        if (!$this->isComposite()) {
            /** @todo */
            //throw new \DomainException("Only composite devices can have native scene info");
        }

        $this->nativeSceneInfo = $info;
    }

    public function hasNativeSupport(): bool
    {
        return ($this->nativeGroupInfo !== null && $this->nativeGroupInfo->isSupported)
            || ($this->nativeSceneInfo !== null && $this->nativeSceneInfo->isSupported);
    }

    public function shouldUseNative(): bool
    {
        if (!$this->hasNativeSupport()) {
            return false;
        }

        return match ($this->compositeStrategy) {
            CompositeStrategy::NATIVE_IF_AVAILABLE, CompositeStrategy::NATIVE_ONLY => true,
            default => false,
        };
    }

    public function setSceneState(string $deviceId, array $state): void
    {
        if (!$this->isComposite()) {
            // @todo
            // throw new \DomainException("Only composite devices can have scene states");
        }

        if ($this->sceneStates === null) {
            $this->sceneStates = [];
        }

        $this->sceneStates[$deviceId] = $state;
    }

    public function setSceneStates(array $states): void
    {
        foreach ($states as $deviceId => $state) {
            $this->setSceneState($deviceId, $state);
        }
    }

    public function isPhysical(): bool
    {
        return $this->type === DeviceType::PHYSICAL;
    }

    public function isVirtual(): bool
    {
        return $this->type === DeviceType::VIRTUAL;
    }

    public function isOnline(): bool
    {
        return $this->status === DeviceStatus::ONLINE;
    }

    public function hasProtocol(): bool
    {
        return $this->protocolId !== null;
    }

    public function hasZone(): bool
    {
        return $this->zoneId !== null;
    }

    public function isScene(): bool
    {
        return $this->isComposite() && $this->sceneStates !== null;
    }

    public function isGroup(): bool
    {
        return $this->isComposite() && $this->sceneStates === null;
    }

    public function isComposite(): bool
    {
        return $this->type === DeviceType::COMPOSITE;
    }
}
