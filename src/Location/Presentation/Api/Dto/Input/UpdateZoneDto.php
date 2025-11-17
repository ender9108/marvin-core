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
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

final class UpdateZoneDto
{
    #[NotBlank(allowNull: true)]
    public ?string $zoneName = null;

    #[Length(min: 0, max: 10000)]
    public ?float $surfaceArea = null;

    #[NotBlank(allowNull: true)]
    #[Choice(choices: [
        Orientation::NORTH->value,
        Orientation::EAST->value,
        Orientation::SOUTH->value,
        Orientation::WEST->value,
        Orientation::NORTH_EAST->value,
        Orientation::NORTH_WEST->value,
        Orientation::SOUTH_EAST->value,
        Orientation::SOUTH_WEST->value,
    ])]
    public ?string $orientation = null;

    public ?float $targetTemperature = null;

    #[Length(min: 0)]
    public ?float $targetPowerConsumption = null;

    #[Length(min: 0, max: 100)]
    public ?float $targetHumidity = null;

    public ?string $icon = null;

    #[Regex(pattern: '/^#([0-9A-Fa-f]{3}|[0-9A-Fa-f]{4}|[0-9A-Fa-f]{6}|[0-9A-Fa-f]{8})$/')]
    public ?string $color = null;

    public ?array $metadata = null;
}
