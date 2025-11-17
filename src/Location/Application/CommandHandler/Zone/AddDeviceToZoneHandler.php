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

namespace Marvin\Location\Application\CommandHandler\Zone;

use Marvin\Location\Application\Command\Zone\AddDeviceToZone;
use Marvin\Location\Domain\Model\Zone;
use Marvin\Location\Domain\Repository\ZoneRepositoryInterface;
use Marvin\Shared\Application\Service\Acl\DeviceQueryServiceInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class AddDeviceToZoneHandler
{
    public function __construct(
        private ZoneRepositoryInterface $zoneRepository,
        private DeviceQueryServiceInterface $deviceQueryService,
    ) {
    }

    public function __invoke(AddDeviceToZone $command): Zone
    {
        $zone = $this->zoneRepository->byId($command->zoneId);
        $device = $this->deviceQueryService->getDevice($command->deviceId);

        $zone->addDevice($command->deviceId);
        $this->zoneRepository->save($zone);

        return $zone;
    }
}
