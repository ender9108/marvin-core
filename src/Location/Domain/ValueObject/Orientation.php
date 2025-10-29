<?php

namespace Marvin\Location\Domain\ValueObject;

use EnderLab\ToolsBundle\Service\EnumToArrayTrait;

enum Orientation: string
{
    use EnumToArrayTrait;

    case NORTH = 'north';
    case SOUTH = 'south';
    case EAST = 'east';
    case WEST = 'west';
    case NORTH_EAST = 'north_east';
    case NORTH_WEST = 'north_west';
    case SOUTH_EAST = 'south_east';
    case SOUTH_WEST = 'south_west';

    public function isNorth(): bool
    {
        return in_array($this, [self::NORTH, self::NORTH_EAST, self::NORTH_WEST], true);
    }

    public function isSouth(): bool
    {
        return in_array($this, [self::SOUTH, self::SOUTH_EAST, self::SOUTH_WEST], true);
    }

    public function isEast(): bool
    {
        return in_array($this, [self::EAST, self::NORTH_EAST, self::SOUTH_EAST], true);
    }

    public function isWest(): bool
    {
        return in_array($this, [self::WEST, self::NORTH_WEST, self::SOUTH_WEST], true);
    }

    /** @todo translate */
    public function getLabel(): string
    {
        return match ($this) {
            self::NORTH => 'Nord',
            self::SOUTH => 'Sud',
            self::EAST => 'Est',
            self::WEST => 'Ouest',
            self::NORTH_EAST => 'Nord-Est',
            self::NORTH_WEST => 'Nord-Ouest',
            self::SOUTH_EAST => 'Sud-Est',
            self::SOUTH_WEST => 'Sud-Ouest',
        };
    }
}
