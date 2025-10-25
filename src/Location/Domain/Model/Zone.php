<?php

namespace Marvin\Location\Domain\Model;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use EnderLab\DddCqrsBundle\Domain\Model\AggregateRoot;
use Marvin\Location\Domain\Event\Zone\ZoneDeleted;
use Marvin\Location\Domain\Event\Zone\ZoneOccupancyChanged;
use Marvin\Location\Domain\Event\Zone\ZoneTemperatureUpdated;
use Marvin\Location\Domain\ValueObject\HexaColor;
use Marvin\Location\Domain\ValueObject\Orientation;
use Marvin\Location\Domain\ValueObject\SurfaceArea;
use Marvin\Location\Domain\ValueObject\TargetPowerConsumption;
use Marvin\Location\Domain\ValueObject\TargetTemperature;
use Marvin\Location\Domain\ValueObject\ZonePath;
use Marvin\Location\Domain\ValueObject\ZoneType;
use Marvin\Shared\Domain\Service\SluggerInterface;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Metadata;

class Zone extends AggregateRoot
{
    public private(set) ZoneId $id;

    public private(set) ?ZonePath $path = null;

    public private(set) ?float $currentTemperature = null;

    public private(set) ?float $currentPowerConsumption = null;

    public private(set) ?bool $isOccupied = null;

    public private(set) ?int $consecutiveNoMotionCount = null;

    public private(set) ?DateTimeInterface $lastMetricsUpdate = null;

    public private(set) Collection $childrens;

    public private(set) ?Zone $parent = null;

    public private(set) ?Label $label = null;

    public private(set) ?string $slug = null;

    public function __construct(
        public readonly ZoneType $type,
        private(set) ?TargetTemperature $targetTemperature = null,
        private(set) ?TargetPowerConsumption $targetPowerConsumption = null,
        private(set) ?string $icon = null,
        private(set) ?SurfaceArea $surfaceArea = null,
        private(set) ?Orientation $orientation = null,
        private(set) ?HexaColor $color = null,
        public readonly ?Metadata $metadata = null,
        public ?DateTimeInterface $updatedAt = null,
        public readonly DateTimeInterface $createdAt = new DateTimeImmutable(),
    ) {
        $this->id = new ZoneId();
        $this->childrens = new ArrayCollection();
    }

    public function delete(): void
    {
        $this->recordThat(new ZoneDeleted(
            $this->id->toString(),
            $this->label->value,
        ));
    }

    public function updateAverageTemperature(?float $temperature): void
    {
        $oldTemp = $this->currentTemperature;
        $this->currentTemperature = $temperature !== null ? round($temperature, 1) : null;
        $this->lastMetricsUpdate = new DateTimeImmutable();

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
    }

    public function markAsOccupied(): void
    {
        $wasOccupied = $this->isOccupied;
        $this->isOccupied = true;
        $this->consecutiveNoMotionCount = 0;
        $this->lastMetricsUpdate = new DateTimeImmutable();

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

    public function updateLabel(Label $label, ?SluggerInterface $slugger = null): void
    {
        $oldLabel = $this->label?->value;
        $this->label = $label;

        if (
            $slugger !== null &&
            $oldLabel !== $label->value
        ) {
            $this->slug = $slugger->slugify($label->value);
        }
    }

    public function updateConfiguration(
        ?SurfaceArea $surfaceArea = null,
        ?Orientation $orientation = null,
        ?TargetTemperature $targetTemperature = null,
        ?TargetPowerConsumption $targetPowerConsumption = null,
        ?string $icon = null,
        ?HexaColor $color = null,
    ): void {
        if ($surfaceArea !== null) {
            $this->surfaceArea = $surfaceArea;
        }
        if ($orientation !== null) {
            $this->orientation = $orientation;
        }
        if ($targetTemperature !== null) {
            $this->targetTemperature = $targetTemperature;
        }
        if ($targetPowerConsumption !== null) {
            $this->targetPowerConsumption = $targetPowerConsumption;
        }
        if ($icon !== null) {
            $this->icon = $icon;
        }
        if ($color !== null) {
            $this->color = $color;
        }
    }

    public function updatePath(ZonePath $path): void
    {
        $this->path = $path;
    }

    public function moveToParent(?Zone $parentZone = null): self
    {
        if (null === $parentZone) {
            $parentZone->removeChildren($this);
        }

        $this->parent = $parentZone;

        $this->updatePath($parentZone->path->append($this->slug));

        return $this;
    }

    public function addChildren(Zone $children): self
    {
        if (!$this->childrens->contains($children)) {
            $this->childrens->add($children);
            $children->moveToParent($this);
        }

        return $this;
    }

    public function removeChildren(Zone $children): self
    {
        if ($this->childrens->contains($children)) {
            $this->childrens->removeElement($children);
            $children->moveToParent(null);
        }
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
