<?php

namespace Marvin\Location\Domain\Model;

use Marvin\Location\Domain\ValueObject\HexaColor;
use Marvin\Location\Domain\ValueObject\Orientation;
use Marvin\Location\Domain\ValueObject\SurfaceArea;
use Marvin\Location\Domain\ValueObject\TargetPowerConsumption;
use Marvin\Location\Domain\ValueObject\TargetTemperature;
use Marvin\Location\Domain\ValueObject\ZonePath;
use Marvin\Location\Domain\ValueObject\ZoneType;
use DateTimeImmutable;
use EnderLab\DddCqrsBundle\Domain\Model\AggregateRoot;
use Marvin\Location\Domain\Event\Zone\ZoneOccupancyChanged;
use Marvin\Location\Domain\Event\Zone\ZoneTemperatureUpdated;
use Marvin\Shared\Domain\ValueObject\CreatedAt;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Metadata;
use Marvin\Shared\Domain\ValueObject\UpdatedAt;

class Zone extends AggregateRoot
{
    public readonly ZoneId $id;

    private(set) ?ZonePath $path = null;

    private(set) ?float $currentTemperature = null;

    private(set) ?float $currentPowerConsumption = null;

    private(set) ?bool $isOccupied = null;

    private(set) ?int $consecutiveNoMotionCount = null;

    private(set) ?DateTimeImmutable $lastMetricsUpdate;

    public function __construct(
        private(set) Label $label,
        private(set) ZoneType $type,
        private(set) ?TargetTemperature $targetTemperature = null,
        private(set) ?TargetPowerConsumption $targetPowerConsumption = null,
        private(set) ?string $icon = null,
        private(set) ?ZoneId $parentZoneId = null,
        private(set) ?SurfaceArea $surfaceArea = null,
        private(set) ?Orientation $orientation = null,
        private(set) ?HexaColor $color = null,
        private(set) ?Metadata $metadata = null,
        private(set) ?UpdatedAt $updatedAt = null,
        public readonly CreatedAt $createdAt = new CreatedAt(new DateTimeImmutable()),
    ) {
        $this->id = new ZoneId();
    }

    public function updateAverageTemperature(?float $temperature): void
    {
        $oldTemp = $this->currentTemperature;
        $this->currentTemperature = $temperature !== null ? round($temperature, 1) : null;
        $this->lastMetricsUpdate = new DateTimeImmutable();
        $this->updatedAt = new UpdatedAt();

        if ($oldTemp !== null && $temperature !== null && abs($oldTemp - $temperature) >= 0.5) {
            $this->recordThat(new ZoneTemperatureUpdated(
                zoneId: $this->id,
                oldTemperature: $oldTemp,
                newTemperature: $temperature,
                occurredAt: new DateTimeImmutable()
            ));
        }
    }

    public function updatePowerConsumption(float $consumption): void
    {
        $this->currentPowerConsumption = round($consumption, 2);
        $this->lastMetricsUpdate = new DateTimeImmutable();
        $this->updatedAt = new UpdatedAt();
    }

    public function markAsOccupied(): void
    {
        $wasOccupied = $this->isOccupied;
        $this->isOccupied = true;
        $this->consecutiveNoMotionCount = 0;
        $this->lastMetricsUpdate = new DateTimeImmutable();
        $this->updatedAt = new UpdatedAt();

        if (!$wasOccupied) {
            $this->recordThat(new ZoneOccupancyChanged(
                zoneId: $this->id,
                isOccupied: true,
                occurredAt: new DateTimeImmutable()
            ));
        }
    }

    public function incrementNoMotionCount(): void
    {
        $this->consecutiveNoMotionCount++;
        $this->lastMetricsUpdate = new DateTimeImmutable();

        if ($this->consecutiveNoMotionCount >= 3 && $this->isOccupied) {
            $this->markAsUnoccupied();
        }
    }

    private function markAsUnoccupied(): void
    {
        $this->isOccupied = false;
        $this->updatedAt = new UpdatedAt();

        $this->recordThat(new ZoneOccupancyChanged(
            zoneId: $this->id,
            isOccupied: false,
            occurredAt: new DateTimeImmutable()
        ));
    }

    public function hasTemperatureAnomaly(float $threshold = 3.0): bool
    {
        if ($this->currentTemperature === null || $this->targetTemperature === null) {
            return false;
        }
        return abs($this->currentTemperature - $this->targetTemperature) > $threshold;
    }

    public function exceedsPowerConsumption(): bool
    {
        if ($this->currentPowerConsumption === null || $this->targetPowerConsumption === null) {
            return false;
        }

        return $this->currentPowerConsumption > $this->targetPowerConsumption;
    }

    public function needsHeating(): bool
    {
        if ($this->currentTemperature === null || $this->targetTemperature === null) {
            return false;
        }

        return $this->currentTemperature < $this->targetTemperature - 0.5;
    }

    public function needsCooling(): bool
    {
        if ($this->currentTemperature === null || $this->targetTemperature === null) {
            return false;
        }

        return $this->currentTemperature > $this->targetTemperature + 0.5;
    }

    public function updateLabel(Label $label): void
    {
        $this->label = $label;

        $this->updatedAt = new UpdatedAt();
    }

    public function updateConfiguration(
        ?SurfaceArea $surfaceArea = null,
        ?Orientation $orientation = null,
        ?float $targetTemperature = null,
        ?float $targetPowerConsumption = null,
        ?string $icon = null,
        ?string $color = null,
    ): void {
        if ($surfaceArea !== null) $this->surfaceArea = $surfaceArea;
        if ($orientation !== null) $this->orientation = $orientation;
        if ($targetTemperature !== null) $this->targetTemperature = $targetTemperature;
        if ($targetPowerConsumption !== null) $this->targetPowerConsumption = $targetPowerConsumption;
        if ($icon !== null) $this->icon = $icon;
        if ($color !== null) $this->color = $color;

        $this->updatedAt = new UpdatedAt();
    }

    public function updatePath(ZonePath $path): void
    {
        $this->path = $path;

        $this->updatedAt = new UpdatedAt();
    }

    public function moveToParent(?ZoneId $newParentZoneId): void
    {
        $this->parentZoneId = $newParentZoneId;

        $this->updatedAt = new UpdatedAt();
    }

    public function hasParent(): bool
    {
        return $this->parentZoneId !== null;
    }

    public function isChildOf(ZoneId $potentialParentId): bool
    {
        return $this->parentZoneId !== null && $this->parentZoneId->equals($potentialParentId);
    }
}
