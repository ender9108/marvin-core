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

namespace Marvin\Location\Presentation\Api\Dto\Input;

use Marvin\Location\Domain\ValueObject\Orientation;
use Marvin\Location\Domain\ValueObject\SurfaceArea;
use Marvin\Location\Domain\ValueObject\ZoneType;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Uuid;

final class CreateZoneDto
{
    #[NotBlank]
    public string $zoneName;

    #[NotBlank]
    #[Choice(choices: [ZoneType::BUILDING, ZoneType::ROOM, ZoneType::FLOOR, ZoneType::OUTDOOR])]
    public string $type;

    #[Uuid(versions: Uuid::V7_MONOTONIC)]
    #[NotBlank(allowNull: true)]
    public ?string $parentZoneId = null;

    #[Length(min: SurfaceArea::MIN_VALUE, max: SurfaceArea::MAX_VALUE)]
    public ?float $surfaceArea = null;

    #[NotBlank(allowNull: true)]
    #[Choice(choices: [
        Orientation::NORTH,
        Orientation::EAST,
        Orientation::SOUTH,
        Orientation::WEST,
        Orientation::NORTH_EAST,
        Orientation::NORTH_WEST,
        Orientation::SOUTH_EAST,
        Orientation::SOUTH_WEST,
    ])]
    public ?string $orientation = null;

    public ?float $targetTemperature = null;

    public ?float $targetHumidity = null;

    public ?float $targetPowerConsumption = null;

    public ?string $icon = null;

    #[Regex(pattern: '/^#([0-9A-Fa-f]{3}|[0-9A-Fa-f]{4}|[0-9A-Fa-f]{6}|[0-9A-Fa-f]{8})$/0')]
    public ?string $color = null;

    public ?array $metadata = null;
}
