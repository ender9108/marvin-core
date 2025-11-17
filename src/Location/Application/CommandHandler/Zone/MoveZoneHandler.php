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

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use Marvin\Location\Application\Command\Zone\MoveZone;
use Marvin\Location\Domain\Exception\InvalidZoneHierarchy;
use Marvin\Location\Domain\Exception\ZoneParentNotFound;
use Marvin\Location\Domain\Model\Zone;
use Marvin\Location\Domain\Repository\ZoneRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class MoveZoneHandler
{
    public function __construct(
        private ZoneRepositoryInterface $zoneRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(MoveZone $command): Zone
    {
        $zone = $this->zoneRepository->byId($command->zoneId);

        if ($command->newParentZoneId?->toString() === $command->zoneId->toString()) {
            throw InvalidZoneHierarchy::circularReference($zone->zoneName);
        }

        $newParentZone = null;

        if ($command->newParentZoneId !== null) {
            try {
                $newParentZone = $this->zoneRepository->byId($command->newParentZoneId);
            } catch (DomainException) {
                throw ZoneParentNotFound::withId($command->newParentZoneId);
            }

            if (!$newParentZone->type->canHaveChildren()) {
                throw InvalidZoneHierarchy::cannotHaveChildren(
                    $newParentZone->zoneName,
                    $newParentZone->type
                );
            }

            foreach ($zone->childrens as $children) {
                if ($children->id->equals($command->newParentZoneId)) {
                    throw InvalidZoneHierarchy::circularReference($zone->zoneName);
                }
            }
        }

        $zone->move($newParentZone);
        $this->zoneRepository->save($zone);
        $this->logger->info('Zone moved', ['zoneId' => $command->zoneId]);

        return $zone;
    }
}
