<?php

namespace Marvin\Location\Domain\Model;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use EnderLab\DddCqrsBundle\Domain\Model\AggregateRoot;
use Marvin\Location\Domain\Event\Zone\ZoneCreated;
use Marvin\Location\Domain\Event\Zone\ZoneDeleted;
use Marvin\Location\Domain\Event\Zone\ZoneUpdated;
use Marvin\Location\Domain\ValueObject\HexaColor;
use Marvin\Location\Domain\ValueObject\Humidity;
use Marvin\Location\Domain\ValueObject\Orientation;
use Marvin\Location\Domain\ValueObject\PowerConsumption;
use Marvin\Location\Domain\ValueObject\SurfaceArea;
use Marvin\Location\Domain\ValueObject\Temperature;
use Marvin\Location\Domain\ValueObject\ZoneName;
use Marvin\Location\Domain\ValueObject\ZoneType;
use Marvin\Shared\Domain\Event\Location\ZoneAverageHumidityCalculated;
use Marvin\Shared\Domain\Event\Location\ZoneAverageTemperatureCalculated;
use Marvin\Shared\Domain\Event\Location\ZonePowerConsumptionUpdated;
use Marvin\Shared\Domain\Event\Location\ZoneSlugUpdated;
use Marvin\Shared\Domain\Service\SluggerInterface;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Domain\ValueObject\Metadata;

class Zone extends AggregateRoot
{
    private(set) ?string $slug = null;

    /* *************** Metrics *************** */
    private(set) ?Temperature $currentTemperature = null;
    private(set) ?PowerConsumption $currentPowerConsumption = null;
    private(set) ?Humidity $currentHumidity = null;
    private(set) bool $isOccupied = false;
    private(set) int $noMotionCounter = 0;
    private(set) int $activeSensorsCount = 0;
    private(set) ?DateTimeInterface $lastMetricsUpdate = null;

    /* *************** Devices *************** */
    private(set) Collection $deviceIds;
    /** @var array<string, float> [deviceId => temperature] */
    private(set) array $deviceTemperatures = [];
    /** @va(set)r array<string, float> [deviceId => humidity] */
    private(set) array $deviceHumidities = [];
    /** @va(set)r array<string, float> [deviceId => power] */
    private(set) array $devicePowerConsumptions = [];

    private(set) Collection $childrens;
    private(set) ?Zone $parent = null;

    public function __construct(
        private(set) ZoneName $zoneName,
        public readonly ZoneType $type,
        private(set) ?ZoneId $id = null,
        private(set) ?Temperature $targetTemperature = null,
        private(set) ?PowerConsumption $targetPowerConsumption = null,
        private(set) ?Humidity $targetHumidity = null,
        private(set) ?string $icon = null,
        private(set) ?SurfaceArea $surfaceArea = null,
        private(set) ?Orientation $orientation = null,
        private(set) ?HexaColor $color = null,
        public ?Metadata $metadata = null,
        public ?DateTimeInterface $updatedAt = null,
        public readonly DateTimeInterface $createdAt = new DateTimeImmutable(),
    ) {
        $this->id = $this->id ?? new ZoneId();
        $this->childrens = new ArrayCollection();
        $this->deviceIds = new ArrayCollection();

        $this->recordEvent(new ZoneCreated(
            $this->id->toString(),
            $this->zoneName->value,
            $this->type->value,
            $this->parent?->id->toString(),
            $this->surfaceArea?->value,
            $this->targetTemperature?->value,
            $this->targetPowerConsumption?->value,
            $this->targetHumidity?->value
        ));
    }

    public function delete(): void
    {
        $this->recordEvent(new ZoneDeleted(
            $this->id->toString(),
            $this->zoneName->value,
        ));
    }

    public function addDevice(DeviceId $deviceId): void
    {
        $deviceIdString = $deviceId->toString();
        if (!$this->deviceIds->contains($deviceIdString)) {
            $this->deviceIds->add($deviceIdString);
        }
    }

    public function removeDevice(DeviceId $deviceId): void
    {
        $deviceIdString = $deviceId->toString();
        if ($this->deviceIds->contains($deviceIdString)) {
            $this->deviceIds->removeElement($deviceIdString);

            // Nettoyer les métriques de ce device
            unset($this->deviceTemperatures[$deviceIdString]);
            unset($this->deviceHumidities[$deviceIdString]);
            unset($this->devicePowerConsumptions[$deviceIdString]);

            // Recalculer les moyennes
            $this->recalculateAggregatedMetrics();
        }
    }

    public function hasDevice(DeviceId $deviceId): bool
    {
        return $this->deviceIds->contains($deviceId->toString());
    }


    public function updateTemperatureFromDevice(DeviceId $deviceId, Temperature $temperature): void
    {
        $deviceIdString = $deviceId->toString();

        if (!$this->hasDevice($deviceId)) {
            throw new \DomainException("Device {$deviceIdString} is not in this zone");
        }

        $this->deviceTemperatures[$deviceIdString] = $temperature->toCelsius();
        $this->activeSensorsCount = count($this->deviceTemperatures);
        $this->recalculateAverageTemperature();
    }

    public function updateHumidityFromDevice(DeviceId $deviceId, Humidity $humidity): void
    {
        $deviceIdString = $deviceId->toString();

        if (!$this->hasDevice($deviceId)) {
            throw new \DomainException("Device {$deviceIdString} is not in this zone");
        }

        $this->deviceHumidities[$deviceIdString] = $humidity->toPercentage();
        $this->recalculateAverageHumidity();
    }

    public function updatePowerConsumptionFromDevice(DeviceId $deviceId, PowerConsumption $power): void
    {
        $deviceIdString = $deviceId->toString();

        if (!$this->hasDevice($deviceId)) {
            throw new \DomainException("Device {$deviceIdString} is not in this zone");
        }

        $this->devicePowerConsumptions[$deviceIdString] = $power->toWatts();
        $this->recalculateTotalPowerConsumption();
    }

    public function updateOccupancyFromDevice(DeviceId $deviceId, bool $motionDetected): void
    {
        if (!$this->hasDevice($deviceId)) {
            throw new \DomainException("Device {$deviceId->toString()} is not in this zone");
        }

        if ($motionDetected) {
            $this->markAsOccupied();
        } else {
            $this->incrementNoMotionCount();
        }
    }


    private function recalculateAverageTemperature(): void
    {
        if (empty($this->deviceTemperatures)) {
            $this->currentTemperature = null;
            return;
        }

        $oldCurrentTemperature = $this->currentTemperature;
        $average = array_sum($this->deviceTemperatures) / count($this->deviceTemperatures);
        $this->currentTemperature = Temperature::fromCelsius($average);

        if ($oldCurrentTemperature !== $this->currentTemperature) {
            $this->recordEvent(new ZoneAverageTemperatureCalculated(
                zoneId: $this->id->toString(),
                zoneName: $this->zoneName->value,
                averageTemperature: $this->currentTemperature->toCelsius(),
                targetTemperature: $this->targetTemperature?->toCelsius(),
                activeSensorsCount: $this->activeSensorsCount,
            ));
        }
    }

    private function recalculateAverageHumidity(): void
    {
        if (empty($this->deviceHumidities)) {
            $this->currentHumidity = null;
            return;
        }

        $oldHumidity = $this->currentHumidity;
        $average = array_sum($this->deviceHumidities) / count($this->deviceHumidities);
        $this->currentHumidity = Humidity::fromPercentage($average);

        if ($oldHumidity !== $this->currentHumidity) {
            $this->recordEvent(new ZoneAverageHumidityCalculated(
                zoneId: $this->id->toString(),
                zoneName: $this->zoneName->value,
                averageHumidity: $this->currentHumidity->toPercentage(),
                targetHumidity: $this->targetHumidity?->toPercentage(),
                activeSensorsCount: $this->activeSensorsCount,
            ));
        }
    }

    private function recalculateTotalPowerConsumption(): void
    {
        if (empty($this->devicePowerConsumptions)) {
            $this->currentPowerConsumption = null;
            return;
        }

        $oldPowerConsumption = $this->currentPowerConsumption;
        $total = array_sum($this->devicePowerConsumptions);
        $this->currentPowerConsumption = PowerConsumption::fromWatts($total);

        if ($oldPowerConsumption !== $this->currentPowerConsumption) {
            $this->recordEvent(new ZonePowerConsumptionUpdated(
                zoneId: $this->id->toString(),
                zoneName: $this->zoneName->value,
                totalPowerConsumption: $this->currentPowerConsumption->toWatts(),
                activeSensorsCount: $this->activeSensorsCount,
            ));
        }
    }

    private function recalculateAggregatedMetrics(): void
    {
        $this->recalculateAverageTemperature();
        $this->recalculateAverageHumidity();
        $this->recalculateTotalPowerConsumption();

    }


    public function markAsOccupied(): self
    {
        $this->isOccupied = true;
        $this->noMotionCounter = 0;

        return $this;
    }

    public function incrementNoMotionCount(): self
    {
        $this->noMotionCounter++;

        // Règle des 3 no-motion : après 3 détections "no motion" consécutives
        if ($this->noMotionCounter >= 3) {
            $this->isOccupied = false;
        }

        return $this;
    }


    /**
     * Vérifie si la température actuelle s'écarte trop de la cible
     */
    public function hasTemperatureAnomaly(float $maxDeltaCelsius = 3.0): bool
    {
        if ($this->currentTemperature === null || $this->targetTemperature === null) {
            return false;
        }

        return $this->currentTemperature->difference($this->targetTemperature) > $maxDeltaCelsius;
    }

    /**
     * Vérifie si la consommation dépasse un budget
     */
    public function exceedsPowerBudget(float $budgetWatts = 2000.0): bool
    {
        if ($this->currentPowerConsumption === null) {
            return false;
        }

        return $this->currentPowerConsumption->exceedsBudget($budgetWatts);
    }

    /**
     * Vérifie si la zone a besoin de chauffage
     */
    public function needsHeating(): bool
    {
        if ($this->currentTemperature === null || $this->targetTemperature === null) {
            return false;
        }

        // Si température actuelle < cible - 0.5°C
        return $this->currentTemperature->toCelsius() < ($this->targetTemperature->toCelsius() - 0.5);
    }

    /**
     * Vérifie si la zone a besoin de climatisation
     */
    public function needsCooling(): bool
    {
        if ($this->currentTemperature === null || $this->targetTemperature === null) {
            return false;
        }

        // Si température actuelle > cible + 0.5°C
        return $this->currentTemperature->toCelsius() > ($this->targetTemperature->toCelsius() + 0.5);
    }

    /**
     * Calcule le delta de température par rapport à la cible
     */
    public function getTemperatureDelta(): ?float
    {
        if ($this->currentTemperature === null || $this->targetTemperature === null) {
            return null;
        }

        return $this->currentTemperature->toCelsius() - $this->targetTemperature->toCelsius();
    }

    public function hasHumidityAnomaly(float $maxDeltaPercentage = 5.0): bool
    {
        if ($this->currentHumidity === null || $this->targetHumidity === null) {
            return false;
        }

        return $this->currentHumidity->difference($this->targetHumidity) > $maxDeltaPercentage;
    }

    public function updateSlug(SluggerInterface $slugger): self
    {
        $oldSlug = $this->slug;
        $this->slug = $slugger->slugify($this->zoneName->value);

        if ($oldSlug !== $this->slug) {
            $this->recordEvent(new ZoneSlugUpdated(
                $this->id->toString(),
                $this->zoneName->value,
                $this->slug,
            ));
        }

        return $this;
    }

    public function updateName(ZoneName $zoneName, SluggerInterface $slugger): self
    {
        $oldZoneName = $this->zoneName;
        $this->zoneName = $zoneName;

        if ($oldZoneName !== $this->zoneName) {
            $this->updateSlug($slugger);
        }

        return $this;
    }

    public function updateConfiguration(
        ?SurfaceArea $surfaceArea = null,
        ?Orientation $orientation = null,
        ?Temperature $targetTemperature = null,
        ?Humidity $targetHumidity = null,
        ?PowerConsumption $targetPowerConsumption = null,
        ?string $icon = null,
        ?HexaColor $color = null,
        ?Metadata $metadata = null,
    ): self {
        $this->surfaceArea = $surfaceArea;
        $this->orientation = $orientation;
        $this->targetTemperature = $targetTemperature;
        $this->targetPowerConsumption = $targetPowerConsumption;
        $this->targetHumidity = $targetHumidity;
        $this->icon = $icon;
        $this->color = $color;
        $this->metadata = $metadata;

        $this->recordEvent(new ZoneUpdated(
            zoneId: $this->id->toString(),
            zoneName: $this->zoneName->value,
            surfaceArea: $this->surfaceArea?->value,
            orientation: $this->orientation?->value,
            targetTemperature: $this->targetTemperature?->value,
            targetPowerConsumption: $this->targetPowerConsumption?->value,
            targetHumidity: $this->targetHumidity?->value
        ));

        return $this;
    }

    public function move(?Zone $parentZone = null): self
    {
        $parentZone?->removeChildren($this);

        $this->parent = $parentZone;

        return $this;
    }

    public function addChildren(Zone $children): self
    {
        if (!$this->childrens->contains($children)) {
            $this->childrens->add($children);
            $children->move($this);
        }

        return $this;
    }

    public function removeChildren(Zone $children): self
    {
        if ($this->childrens->contains($children)) {
            $this->childrens->removeElement($children);
            $children->move(null);
        }

        return $this;
    }

    public function hasParent(): bool
    {
        return $this->parent !== null;
    }

    public function hasChildren(): bool
    {
        return !$this->childrens->isEmpty();
    }

    public function isChildrenOf(Zone $potentialParent): bool
    {
        return $potentialParent->childrens->contains($this);
    }
}
