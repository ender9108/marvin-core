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

namespace Marvin\Shared\Application\Service\Acl\Dto;

final readonly class ZoneDto
{
    public function __construct(
        public string $id,
        public string $name,
        public ?float $targetTemperature = null,
        public ?float $targetPowerConsumption = null,
        public ?float $targetHumidity = null,
        public ?float $currentTemperature = null,
        public ?float $currentPowerConsumption = null,
        public ?float $currentHumidity = null,
        public bool $isOccupied = false,
        public ?string $parentId = null,
        public array $metadata = [],
        public array $childrenId = [],
    ) {
    }
}
