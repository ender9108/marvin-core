<?php

namespace Marvin\Device\Domain\Model;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\DddCqrsBundle\Domain\Model\AggregateRoot;
use Marvin\Device\Domain\Event\Device\DeviceActionExecuted;
use Marvin\Device\Domain\Event\Device\DeviceAssignedToZone;
use Marvin\Device\Domain\Event\Device\DeviceCreated;
use Marvin\Device\Domain\Event\Device\DeviceDeleted;
use Marvin\Device\Domain\Event\Device\DeviceOffline;
use Marvin\Device\Domain\Event\Device\DeviceOnline;
use Marvin\Device\Domain\Event\Device\DeviceStateChanged;
use Marvin\Device\Domain\Exception\CapabilityNotFound;
use Marvin\Device\Domain\Exception\CapabilityNotSupportedAction;
use Marvin\Device\Domain\Exception\DeviceCompositeCircularReference;
use Marvin\Device\Domain\Exception\DeviceMustBeComposite;
use Marvin\Device\Domain\ValueObject\Capability;
use Marvin\Device\Domain\ValueObject\CompositeStrategy;
use Marvin\Device\Domain\ValueObject\CompositeType;
use Marvin\Device\Domain\ValueObject\DeviceStatus;
use Marvin\Device\Domain\ValueObject\DeviceType;
use Marvin\Device\Domain\ValueObject\NativeGroupInfo;
use Marvin\Device\Domain\ValueObject\NativeSceneInfo;
use Marvin\Device\Domain\ValueObject\SceneStates;
use Marvin\Device\Domain\ValueObject\TechnicalName;
use Marvin\Device\Domain\ValueObject\VirtualDeviceConfig;
use Marvin\Device\Domain\ValueObject\VirtualDeviceType;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;
use Marvin\Shared\Domain\ValueObject\Identity\ProtocolId;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Metadata;

class Device extends AggregateRoot
{
    /**
     * @var Collection<int, DeviceCapability>
     */
    public private(set) Collection $capabilities;

    /**
     * @var Collection<int, DeviceState>
     */
    public private(set) Collection $states;

    /**
     * @var Collection<int, Device>
     */
    public private(set) Collection $childrens;

    public private(set) ?Device $parent = null;

    public function __construct(
        private(set) Label $label,
        private(set) DeviceType $type,
        private(set) DeviceStatus $status,
        private(set) ?ProtocolId $protocolId = null,
        private(set) ?TechnicalName $technicalName = null,
        private(set) ?VirtualDeviceType $virtualDeviceType = null,
        private(set) ?VirtualDeviceConfig $virtualDeviceConfig = null,
        private(set) ?NativeGroupInfo $nativeGroupInfo = null,
        private(set) ?NativeSceneInfo $nativeSceneInfo = null,
        private(set) ?SceneStates $sceneStates = null, // Pour les scènes : états par device
        private(set) ?CompositeStrategy $compositeStrategy = null,
        private(set) ?ZoneId $zoneId = null,
        private(set) ?string $manufacturer = null,
        private(set) ?string $model = null,
        private(set) ?string $firmwareVersion = null,
        private(set) ?Metadata $metadata = null,
        private(set) ?DateTimeInterface $updatedAt = null,
        private(set) DateTimeInterface $createdAt = new DateTimeImmutable(),
        private(set) DeviceId $id = new DeviceId(),
    ) {
        $this->capabilities = new ArrayCollection();
        $this->states = new ArrayCollection();
        $this->childrens = new ArrayCollection();
    }

    public static function createPhysical(
        Label $label,
        ProtocolId $protocolId,
        TechnicalName $technicalName,
        ?string $manufacturer = null,
        ?string $model = null,
        ?string $firmwareVersion = null,
        ?Metadata $metadata = null
    ): self {
        $device = new self(
            label: $label,
            type: DeviceType::PHYSICAL,
            status: DeviceStatus::OFFLINE,
            protocolId: $protocolId,
            technicalName: $technicalName,
            manufacturer: $manufacturer,
            model: $model,
            firmwareVersion: $firmwareVersion,
            metadata: $metadata,
        );

        $device->recordEvent(new DeviceCreated(
            deviceId: $device->id->toString(),
            label: $device->label->value,
            type: $device->type->value,
            protocolId: $device->protocolId->toString(),
            zoneId: $device->zoneId?->toString()
        ));

        return $device;
    }

    public static function createVirtual(
        Label $label,
        VirtualDeviceType $virtualDeviceType,
        VirtualDeviceConfig $virtualDeviceConfig
    ): self {
        $device = new self(
            label: $label,
            type: DeviceType::VIRTUAL,
            status: DeviceStatus::ONLINE,
            virtualDeviceType: $virtualDeviceType,
            virtualDeviceConfig: $virtualDeviceConfig,
        );

        $device->recordEvent(new DeviceCreated(
            deviceId: $device->id->toString(),
            label: $device->label->value,
            type: $device->type->value,
            virtualDeviceType: $device->virtualDeviceType->value
        ));

        return $device;
    }

    public static function createGroup(
        Label $label,
        /** @var Device[] $childrens */
        array $childrens,
        CompositeStrategy $strategy = CompositeStrategy::NATIVE_IF_AVAILABLE
    ): self {
        $device = new self(
            label: $label,
            type: DeviceType::COMPOSITE,
            status: DeviceStatus::ONLINE,
            compositeStrategy: $strategy,
        );

        $device->setChildrens($childrens);

        $device->recordEvent(new DeviceCreated(
            deviceId: $device->id->toString(),
            label: $label->value,
            type: DeviceType::COMPOSITE->value,
            compositeType: CompositeType::GROUP->value,
            childCount: count($childrens)
        ));

        return $device;
    }

    public static function createScene(
        Label $label,
        array $devicesWithStates,
        CompositeStrategy $strategy = CompositeStrategy::NATIVE_IF_AVAILABLE
    ): self {
        $device = new self(
            label: $label,
            type: DeviceType::COMPOSITE,
            status: DeviceStatus::ONLINE,
            compositeStrategy: $strategy,
        );

        $currentStates = [];

        /** @var Device $deviceWithStates */
        foreach ($devicesWithStates as $deviceWithStates) {
            Assert::isInstanceOf(
                $deviceWithStates,
                Device::class,
                'device.exceptions.DE0028.device_must_be_an_instance_of_device'
            );

            $device->addChildren($deviceWithStates);
            $currentStates[$deviceWithStates->id->toString()] = $deviceWithStates->states->toArray();
        }

        $device->setSceneStates($currentStates);

        $device->recordEvent(new DeviceCreated(
            deviceId: $device->id->toString(),
            label: $label->value,
            type: DeviceType::COMPOSITE->value,
            compositeType: CompositeType::SCENE->value,
            childCount: count($device->childrens)
        ));

        return $device;
    }

    public function setParent(Device $parent): void
    {
        if ($this->id === $parent->id) {
            throw new DeviceCompositeCircularReference("A device cannot be its own parent");
        }

        $this->parent = $parent;
    }

    public function delete(): void
    {
        $this->recordEvent(new DeviceDeleted(
            deviceId: $this->id->toString(),
            name: $this->label->value,
        ));
    }

    public function addCapability(DeviceCapability $capability): self
    {
        if (!$this->capabilities->contains($capability)) {
            $this->capabilities->add($capability);
        }

        return $this;
    }

    public function removeCapability(DeviceCapability $capability): self
    {
        if (!$this->capabilities->contains($capability)) {
            $this->capabilities->removeElement($capability);
            $capability->setDevice(null);

            $state = $this->findStateCapability($capability->name);

            if (null !== $state) {
                $this->removeState($state);
            }
        }

        return $this;
    }

    public function addState(DeviceState $state): self
    {
        if (!$this->states->contains($state)) {
            $this->states->add($state);
        }

        return $this;
    }

    public function removeState(DeviceState $state): self
    {
        if (!$this->states->contains($state)) {
            $this->states->removeElement($state);
            $state->setDevice(null);
        }

        return $this;
    }

    public function setChildrens(array $childrens): void
    {
        $this->childrens = new ArrayCollection();

        foreach ($childrens as $children) {
            $this->addChildren($children);
        }
    }

    public function addChildren(Device $device): self
    {
        if (!$this->isComposite()) {
            throw new DeviceMustBeComposite("Only composite devices can have child devices");
        }

        if ($this->id === $device->id) {
            throw new DeviceCompositeCircularReference("A device cannot be its own child");
        }

        if (!$this->childrens->contains($device)) {
            $this->childrens->add($device);
        }

        return $this;
    }

    public function removeChildren(Device $device): self
    {
        if (!$this->isComposite()) {
            throw new DeviceMustBeComposite("Only composite devices can have child devices");
        }

        if (!$this->childrens->contains($device)) {
            $this->childrens->removeElement($device);
        }

        return $this;
    }

    public function updateState(Capability $capability, mixed $value, ?string $unit = null): void
    {
        $state = $this->getOrCreateState($capability);
        $oldValue = $state->value;

        $state->updateValue($value, $unit);

        $this->recordEvent(new DeviceStateChanged(
            deviceId: $this->id->toString(),
            capability: $capability->value,
            oldValue: $oldValue,
            newValue: $value
        ));
    }

    public function executeAction(Capability $capability, string $action, array $params = []): void
    {
        $deviceCapability = $this->findCapability($capability);

        if (!$deviceCapability) {
            throw CapabilityNotFound::withCapabilityAndDevice(
                $capability,
                $this->label,
            );
        }

        if (!$deviceCapability->supportsAction($action)) {
            throw CapabilityNotSupportedAction::withCapabilityAndAction(
                $capability,
                $action,
            );
        }

        $this->recordEvent(new DeviceActionExecuted(
            deviceId: $this->id->toString(),
            capability: $capability->value,
            action: $action,
            params: $params
        ));
    }

    public function markOnline(): void
    {
        if ($this->status !== DeviceStatus::ONLINE) {
            $this->status = DeviceStatus::ONLINE;

            $this->recordEvent(new DeviceOnline(
                deviceId: $this->id->toString(),
                label: $this->label
            ));
        }
    }

    public function markOffline(): void
    {
        if ($this->status !== DeviceStatus::OFFLINE) {
            $this->status = DeviceStatus::OFFLINE;

            $this->recordEvent(new DeviceOffline(
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

        $this->recordEvent(new DeviceAssignedToZone(
            deviceId: $this->id->toString(),
            zoneId: $zoneId->toString(),
            previousZoneId: $oldZoneId
        ));
    }

    public function hasZone(): bool
    {
        return $this->zoneId !== null;
    }

    public function unassignFromZone(): void
    {
        if ($this->zoneId !== null) {
            $oldZoneId = $this->zoneId->toString();
            $this->zoneId = null;

            $this->recordEvent(new DeviceAssignedToZone(
                deviceId: $this->id->toString(),
                zoneId: null,
                previousZoneId: $oldZoneId
            ));
        }
    }

    public function getState(Capability $capability): ?DeviceState
    {
        /** @var DeviceState $state */
        foreach ($this->states as $state) {
            if ($state->capability->equals($capability)) {
                return $state;
            }
        }
        return null;
    }

    public function setNativeGroupInfo(NativeGroupInfo $info): void
    {
        if (!$this->isComposite()) {
            throw new DeviceMustBeComposite("Only composite devices can have native group info");
        }

        $this->nativeGroupInfo = $info;
    }

    public function setNativeSceneInfo(NativeSceneInfo $info): void
    {
        if (!$this->isComposite()) {
            throw new DeviceMustBeComposite("Only composite devices can have native scene info");
        }

        $this->nativeSceneInfo = $info;
    }

    public function hasNativeSupport(): bool
    {
        return
            ($this->nativeGroupInfo !== null && $this->nativeGroupInfo->isSupported) ||
            ($this->nativeSceneInfo !== null && $this->nativeSceneInfo->isSupported)
        ;
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

    public function setSceneState(DeviceId $deviceId, array $state): void
    {
        if (!$this->isComposite()) {
            throw new DeviceMustBeComposite("Only composite devices can have scene states");
        }

        $previousState = [];

        if (null !== $this->sceneStates) {
            $previousState = $this->sceneStates->toArray();
        }

        $this->sceneStates = SceneStates::fromArray(array_merge(
            $previousState,
            [$deviceId->toString() => $state]
        ));
    }

    public function setSceneStates(array $states): void
    {
        foreach ($states as $deviceId => $state) {
            $this->setSceneState(DeviceId::fromString($deviceId), $state);
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

    public function isComposite(): bool
    {
        return $this->type === DeviceType::COMPOSITE;
    }

    public function isOnline(): bool
    {
        return $this->status === DeviceStatus::ONLINE;
    }

    public function hasProtocol(): bool
    {
        return $this->protocolId !== null;
    }

    public function isScene(): bool
    {
        return
            $this->isComposite() &&
            $this->nativeSceneInfo !== null &&
            $this->sceneStates !== null
        ;
    }

    public function isGroup(): bool
    {
        return
            $this->isComposite() &&
            $this->nativeGroupInfo !== null &&
            $this->sceneStates === null
        ;
    }

    private function getOrCreateState(Capability $capability): DeviceState
    {
        /** @var DeviceState $state */
        foreach ($this->states as $state) {
            if ($state->capability->equals($capability)) {
                return $state;
            }
        }

        $newState = new DeviceState($capability);
        $this->states->add($newState);

        return $newState;
    }

    private function findCapability(Capability $capability): ?DeviceCapability
    {
        $capabilityFound = $this->capabilities->filter(
            fn (DeviceCapability $deviceCapability) => $deviceCapability->capability->equals($capability)
        )->first();

        return false === $capabilityFound ? null : $capabilityFound;
    }

    private function findStateCapability(Capability $capability): ?DeviceState
    {
        $stateFound = $this->states->filter(fn (DeviceState $state) => $state->capability->equals($capability))->first();

        return false === $stateFound ? null : $stateFound;
    }
}
