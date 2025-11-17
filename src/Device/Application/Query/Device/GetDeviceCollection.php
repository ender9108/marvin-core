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

namespace Marvin\Device\Application\Query\Device;

use EnderLab\DddCqrsBundle\Application\Query\QueryInterface;
use Marvin\Device\Domain\ValueObject\Capability;
use Marvin\Device\Domain\ValueObject\DeviceStatus;
use Marvin\Device\Domain\ValueObject\DeviceType;
use Marvin\Device\Domain\ValueObject\Protocol;
use Marvin\Shared\Domain\ValueObject\Identity\ProtocolId;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;

/**
 * Query to get a collection of devices with optional filters
 */
final readonly class GetDeviceCollection implements QueryInterface
{
    public function __construct(
        public ?Protocol $protocol = null,
        public ?ProtocolId $protocolId = null,
        public ?ZoneId $zoneId = null,
        public ?DeviceType $deviceType = null,
        public ?DeviceStatus $status = null,
        public ?Capability $capability = null,
        public int $page = 1,
        public int $limit = 50,
    ) {
    }
}
