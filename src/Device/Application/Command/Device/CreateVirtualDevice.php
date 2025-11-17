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

namespace Marvin\Device\Application\Command\Device;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Device\Domain\ValueObject\Capability;
use Marvin\Device\Domain\ValueObject\VirtualDeviceConfig;
use Marvin\Device\Domain\ValueObject\VirtualDeviceType;
use Marvin\Shared\Domain\ValueObject\Description;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Metadata;

/**
 * Command to create a new virtual device (TIME, WEATHER, HTTP)
 *
 * Virtual devices fetch data from external sources:
 * - TIME: Sun times (sunrise, sunset, twilight) based on location
 * - WEATHER: Weather data (temperature, humidity, pressure) from OpenWeatherMap
 * - HTTP: Custom sensor data from any REST API
 */
final readonly class CreateVirtualDevice implements SyncCommandInterface
{
    /**
     * @param Capability[] $capabilities List of device capabilities
     */
    public function __construct(
        public Label $label,
        public VirtualDeviceType $virtualType,
        public VirtualDeviceConfig $virtualConfig,
        public array $capabilities = [],
        public ?ZoneId $zoneId = null,
        public ?Description $description = null,
        public ?Metadata $metadata = null,
    ) {
    }
}
