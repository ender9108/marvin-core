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

namespace Marvin\Device\Domain\Model;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use EnderLab\DddCqrsBundle\Domain\Model\AggregateRoot;
use Marvin\Device\Domain\Event\Device\DeviceCreated;
use Marvin\Device\Domain\Event\Device\DeviceRemovedFromGroup;
use Marvin\Device\Domain\Event\Device\DeviceStateChanged;
use Marvin\Device\Domain\Event\Scene\SceneStatesUpdated;
use Marvin\Device\Domain\Exception\RemoveChildrenNotAuthorized;
use Marvin\Device\Domain\Exception\UpdateSceneStateNotAuthorized;
use Marvin\Device\Domain\ValueObject\Capability;
use Marvin\Device\Domain\ValueObject\CompositeStrategy;
use Marvin\Device\Domain\ValueObject\CompositeType;
use Marvin\Device\Domain\ValueObject\DeviceStatus;
use Marvin\Device\Domain\ValueObject\DeviceType;
use Marvin\Device\Domain\ValueObject\ExecutionStrategy;
use Marvin\Device\Domain\ValueObject\NativeGroupInfo;
use Marvin\Device\Domain\ValueObject\NativeSceneInfo;
use Marvin\Device\Domain\ValueObject\PhysicalAddress;
use Marvin\Device\Domain\ValueObject\Protocol;
use Marvin\Device\Domain\ValueObject\SceneStates;
use Marvin\Device\Domain\ValueObject\TechnicalName;
use Marvin\Device\Domain\ValueObject\VirtualDeviceConfig;
use Marvin\Device\Domain\ValueObject\VirtualDeviceType;
use Marvin\Shared\Domain\ValueObject\Description;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;
use Marvin\Shared\Domain\ValueObject\Identity\ProtocolId;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Metadata;

/**
 * Device - Aggregate Root
 *
 * Représente un équipement dans le système domotique (physique, virtuel ou composite)
 */
final class Device extends AggregateRoot
{
    /** @var Collection<int, DeviceCapability> */
    public private(set) Collection $capabilities;

    private function __construct(
        private(set) Label $label,
        private(set) ?Description $description = null,
        private(set) ?DeviceType $deviceType = null,
        private(set) ?DeviceStatus $status = null,

        // Physical device properties
        private(set) ?Protocol $protocol = null,
        private(set) ?ProtocolId $protocolId = null,
        private(set) ?PhysicalAddress $physicalAddress = null,
        private(set) ?TechnicalName $technicalName = null,

        // Composite device properties
        private(set) ?CompositeType $compositeType = null,
        private(set) ?CompositeStrategy $compositeStrategy = null,
        private(set) ?ExecutionStrategy $executionStrategy = null,
        /** @var DeviceId[] $childDeviceIds */
        private(set) array $childDeviceIds = [],
        private(set) ?NativeGroupInfo $nativeGroupInfo = null,
        private(set) array $nativeSubGroups = [],
        private(set) ?NativeSceneInfo $nativeSceneInfo = null,
        private(set) ?SceneStates $sceneStates = null,

        // Virtual device properties
        private(set) ?VirtualDeviceType $virtualType = null,
        private(set) ?VirtualDeviceConfig $virtualConfig = null,

        // Common properties
        private(set) ?ZoneId $zoneId = null,
        private(set) ?Metadata $metadata = null,
        private(set) DateTimeImmutable $createdAt = new DatetimeImmutable(),
        private(set) ?DateTimeImmutable $lastSeenAt = null,
        private(set) ?DateTimeImmutable $lastStateUpdateAt = null,
        private(set) DeviceId $id = new DeviceId(),
    ) {
        $this->capabilities = new ArrayCollection();
    }

    /**
     * Crée un device physique (ACTUATOR ou SENSOR)
     */
    public static function createPhysical(
        Label $label,
        DeviceType $deviceType,
        Protocol $protocol,
        ProtocolId $protocolId,
        PhysicalAddress $physicalAddress,
        TechnicalName $technicalName,
        array|Collection $capabilities,
        ?ZoneId $zoneId = null,
        ?Description $description = null,
        ?Metadata $metadata = null,
    ): self {
        $now = new DateTimeImmutable();

        $device = new self(
            label: $label,
            description: $description,
            deviceType: $deviceType,
            status: DeviceStatus::UNKNOWN,
            protocol: $protocol,
            protocolId: $protocolId,
            physicalAddress: $physicalAddress,
            technicalName: $technicalName,
            compositeType: null,
            compositeStrategy: null,
            executionStrategy: null,
            childDeviceIds: [],
            nativeGroupInfo: null,
            nativeSubGroups: [],
            nativeSceneInfo: null,
            sceneStates: null,
            virtualType: null,
            virtualConfig: null,
            zoneId: $zoneId,
            metadata: $metadata ?? Metadata::empty(),
            createdAt: $now,
            lastSeenAt: null,
            lastStateUpdateAt: null,
        );

        foreach ($capabilities as $key => $capability) {
            if ($capability instanceof DeviceCapability) {
                $device->addCapability($capability);
            } else {
                $device->createAndAddCapability(
                    Capability::from($key),
                    $capability['stateName'],
                    $capability['initialValue'] ?? null,
                    $capability['unit'] ?? null,
                );
            }
        }

        $device->recordEvent(new DeviceCreated(
            deviceId: $device->id->toString(),
            label: $label->value,
            deviceType: $deviceType->value,
            protocol: $protocol->value,
        ));

        return $device;
    }

    /**
     * Crée un device virtuel (TIME, WEATHER, HTTP)
     */
    public static function createVirtual(
        Label $label,
        VirtualDeviceType $virtualType,
        VirtualDeviceConfig $virtualConfig,
        array|Collection $capabilities,
        ?ZoneId $zoneId = null,
        ?Description $description = null,
        ?Metadata $metadata = null,
    ): self {
        $now = new DateTimeImmutable();

        $device = new self(
            label: $label,
            description: $description,
            deviceType: DeviceType::VIRTUAL,
            status: DeviceStatus::UNKNOWN,
            protocol: null,
            protocolId: null,
            physicalAddress: null,
            technicalName: null,
            compositeType: null,
            compositeStrategy: null,
            executionStrategy: null,
            childDeviceIds: [],
            nativeGroupInfo: null,
            nativeSubGroups: [],
            nativeSceneInfo: null,
            sceneStates: null,
            virtualType: $virtualType,
            virtualConfig: $virtualConfig,
            zoneId: $zoneId,
            metadata: $metadata ?? Metadata::empty(),
            createdAt: $now,
            lastSeenAt: null,
            lastStateUpdateAt: null,
        );

        foreach ($capabilities as $key => $capability) {
            if ($capability instanceof DeviceCapability) {
                $device->addCapability($capability);
            } else {
                $device->createAndAddCapability(
                    Capability::from($key),
                    $capability['stateName'],
                    $capability['initialValue'] ?? null,
                    $capability['unit'] ?? null,
                );
            }
        }

        $device->recordEvent(new DeviceCreated(
            deviceId: $device->id->toString(),
            label: $label->value,
            deviceType: DeviceType::VIRTUAL->value,
            protocol: null,
        ));

        return $device;
    }

    /**
     * Crée un device composite (GROUP ou SCENE)
     */
    public static function createComposite(
        Label $label,
        CompositeType $compositeType,
        array $childDeviceIds,
        array|Collection $capabilities,
        CompositeStrategy $compositeStrategy,
        ?ExecutionStrategy $executionStrategy = null,
        ?NativeGroupInfo $nativeGroupInfo = null,
        array $nativeSubGroups = [],
        ?NativeSceneInfo $nativeSceneInfo = null,
        ?SceneStates $sceneStates = null,
        ?ZoneId $zoneId = null,
        ?Description $description = null,
        ?Metadata $metadata = null,
    ): self {
        $now = new DateTimeImmutable();

        $device = new self(
            label: $label,
            description: $description,
            deviceType: DeviceType::COMPOSITE,
            status: DeviceStatus::ONLINE, // Composites toujours online
            protocol: null,
            protocolId: null,
            physicalAddress: null,
            technicalName: null,
            compositeType: $compositeType,
            compositeStrategy: $compositeStrategy,
            executionStrategy: $executionStrategy ?? ExecutionStrategy::BROADCAST,
            childDeviceIds: $childDeviceIds,
            nativeGroupInfo: $nativeGroupInfo,
            nativeSubGroups: $nativeSubGroups,
            nativeSceneInfo: $nativeSceneInfo,
            sceneStates: $sceneStates,
            virtualType: null,
            virtualConfig: null,
            zoneId: $zoneId,
            metadata: $metadata ?? Metadata::empty(),
            createdAt: $now,
            lastSeenAt: $now,
            lastStateUpdateAt: null,
        );

        foreach ($capabilities as $key => $capability) {
            if ($capability instanceof DeviceCapability) {
                $device->addCapability($capability);
            } else {
                $device->createAndAddCapability(
                    Capability::from($key),
                    $capability['stateName'],
                    $capability['initialValue'] ?? null,
                    $capability['unit'] ?? null,
                );
            }
        }

        $device->recordEvent(new DeviceCreated(
            deviceId: $device->id->toString(),
            label: $label->value,
            deviceType: DeviceType::COMPOSITE->value,
            protocol: null,
        ));

        return $device;
    }

    public function addCapability(DeviceCapability $capability): void
    {
        if (!$this->capabilities->contains($capability)) {
            $this->capabilities->add($capability);
            $capability->setDevice($this);
        }
    }

    public function removeCapability(DeviceCapability $capability): void
    {
        if ($this->capabilities->contains($capability)) {
            $this->capabilities->removeElement($capability);
            $capability->setDevice(null);
        }
    }

    public function createAndAddCapability(
        Capability $capability,
        string $stateName,
        mixed $initialValue = null,
        ?string $unit = null,
    ): void {
        // Vérifier si ce state existe déjà
        if (array_any($this->capabilities, fn ($existingCap) => $existingCap->stateName === $stateName)) {
            return; // State déjà ajouté
        }

        $capability = DeviceCapability::create($capability, $stateName, $initialValue);

        if (null !== $unit) {
            $capability->setUnit($unit);
        }

        $this->addCapability($capability);
    }

    /**
     * Met à jour l'état du device
     */
    public function updateState(array $newStates): void
    {
        $oldState = $this->getCurrentState();
        $hasChanges = false;

        foreach ($newStates as $capabilityName => $values) {
            foreach ($this->capabilities as $deviceCapability) {
                if (
                    $deviceCapability->capability->value === $capabilityName &&
                    $deviceCapability->stateName === $values['stateName']
                ) {
                    $deviceCapability->updateValue($values['newValue']);

                    if (isset($values['unit'])) {
                        $deviceCapability->setUnit($values['unit']);
                    }

                    $hasChanges = true;
                    break;
                }
            }
        }

        if ($hasChanges) {
            $this->lastStateUpdateAt = new DateTimeImmutable();
            $this->recordEvent(new DeviceStateChanged(
                deviceId: $this->id->toString(),
                oldState: $oldState,
                newState: $this->getCurrentState(),
            ));
        }
    }

    /**
     * Met à jour un état partiel du device (un seul state)
     *
     * Utilisé principalement par les event handlers MQTT/Protocol
     * qui reçoivent des mises à jour incrémentales
     *
     * @param string $stateName Nom du state (ex: "brightness", "is_heating", "state")
     * @param mixed $value Nouvelle valeur
     * @param string|null $unit Unité optionnelle (ex: "°C", "%", "lux")
     */
    public function updatePartialState(string $stateName, mixed $value, ?string $unit = null): void
    {
        foreach ($this->capabilities as $deviceCapability) {
            if ($deviceCapability->stateName === $stateName) {
                $deviceCapability->updateValue($value);

                // Store unit in metadata if provided
                if ($unit !== null) {
                    $deviceCapability->setUnit($unit);
                }

                $this->lastStateUpdateAt = new DateTimeImmutable();

                // Note: We don't record DeviceStateChanged event here to avoid
                // flooding the event bus with partial updates. The event handler
                // will save the device, and full state events can be triggered
                // by updateState() when needed.

                return;
            }
        }
    }

    /**
     * Marque le device comme online
     */
    public function markOnline(): void
    {
        $this->status = DeviceStatus::ONLINE;
        $this->lastSeenAt = new DateTimeImmutable();
    }

    /**
     * Marque le device comme offline
     */
    public function markOffline(): void
    {
        $this->status = DeviceStatus::OFFLINE;
    }

    /**
     * Assigne le device à une zone
     */
    public function assignToZone(ZoneId $zoneId): void
    {
        $this->zoneId = $zoneId;
    }

    /**
     * Retire le device de sa zone
     */
    public function removeFromZone(): void
    {
        $this->zoneId = null;
    }

    /**
     * Retire un device enfant du groupe composite
     *
     * @throws RemoveChildrenNotAuthorized Si le device n'est pas composite
     */
    public function removeChildDevice(DeviceId $deviceId): void
    {
        if (!$this->isComposite()) {
            throw new RemoveChildrenNotAuthorized('Cannot remove child device from non-composite device');
        }

        // Rechercher et retirer le device enfant
        $originalCount = count($this->childDeviceIds);
        $this->childDeviceIds = array_filter(
            $this->childDeviceIds,
            fn (DeviceId $childId) => !$childId->equals($deviceId)
        );

        // Réindexer le tableau pour éviter les trous dans les clés
        $this->childDeviceIds = array_values($this->childDeviceIds);

        // Si un device a été retiré, enregistrer l'événement
        if (count($this->childDeviceIds) < $originalCount) {
            $this->recordEvent(new DeviceRemovedFromGroup(
                groupId: $this->id->toString(),
                deviceId: $deviceId->toString(),
                groupLabel: $this->label->value,
            ));
        }
    }

    /**
     * Retourne l'état actuel du device sous forme de tableau
     * @todo check si ça fonctionne bien
     */
    public function getCurrentState(): array
    {
        $state = [];

        foreach ($this->capabilities as $capability) {
            $capabilityState = $capability->toStateArray();
            $state = array_merge($state, $capabilityState);
        }

        return $state;
    }

    /**
     * Vérifie si le device supporte une capability
     */
    public function hasCapability(Capability $capability): bool
    {
        foreach ($this->capabilities as $deviceCapability) {
            if ($deviceCapability->capability === $capability) {
                return true;
            }
        }

        return false;
    }

    /**
     * Vérifie si le device est un device physique
     */
    public function isPhysical(): bool
    {
        return $this->deviceType === DeviceType::ACTUATOR || $this->deviceType === DeviceType::SENSOR;
    }

    /**
     * Vérifie si le device est un device composite
     */
    public function isComposite(): bool
    {
        return $this->deviceType === DeviceType::COMPOSITE;
    }

    /**
     * Vérifie si le device est un device virtuel
     */
    public function isVirtual(): bool
    {
        return $this->deviceType === DeviceType::VIRTUAL;
    }

    /**
     * Vérifie si le device est en lecture seule (sensor)
     */
    public function isReadOnly(): bool
    {
        return $this->deviceType === DeviceType::SENSOR;
    }

    /**
     * Mise à jour des états de scène pour un dispositif composite de scène
     *
     * Remplace les états actuellement enregistrés par de nouveaux états.
     * Utilisé par la commande StoreSceneCurrentState pour créer un snapshot des états des périphériques.
     *
     * @throws UpdateSceneStateNotAuthorized if device is not a scene
     */
    public function updateSceneStates(SceneStates $newStates): void
    {
        if (!$this->isComposite() || $this->compositeType !== CompositeType::SCENE) {
            throw new UpdateSceneStateNotAuthorized('Cannot update scene states on non-scene device');
        }

        $this->sceneStates = $newStates;

        $this->recordEvent(new SceneStatesUpdated(
            sceneId: $this->id->toString(),
            sceneLabel: $this->label->value,
            deviceCount: count($newStates->getDeviceIds()),
        ));
    }
}
