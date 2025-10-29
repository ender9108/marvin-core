<?php

declare(strict_types=1);

namespace Marvin\Location\Domain\ValueObject;

use EnderLab\ToolsBundle\Service\EnumToArrayTrait;

enum ZoneType: string
{
    use EnumToArrayTrait;

    case BUILDING = 'building';
    case FLOOR = 'floor';
    case ROOM = 'room';
    case OUTDOOR = 'outdoor';

    public function equals(self $other): bool
    {
        return $other instanceof self && $this->value === $other->value;
    }

    public function isBuilding(): bool
    {
        return $this === self::BUILDING;
    }

    public function isFloor(): bool
    {
        return $this === self::FLOOR;
    }

    public function isRoom(): bool
    {
        return $this === self::ROOM;
    }

    public function isOutdoor(): bool
    {
        return $this === self::OUTDOOR;
    }

    public function canHaveChildren(): bool
    {
        return $this === self::BUILDING || $this === self::FLOOR;
    }

    public function label(): string
    {
        return match ($this) {
            self::BUILDING => 'location.type.building',
            self::FLOOR => 'location.type.floor',
            self::ROOM => 'location.type.room',
            self::OUTDOOR => 'location.type.outdoor',
        };
    }
}
