<?php

namespace MarvinTests\Location\Application\Command;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use Marvin\Location\Application\Command\Zone\CreateZone;
use Marvin\Location\Application\Command\Zone\DeleteZone;
use Marvin\Location\Domain\Exception\InvalidZoneHierarchy;
use Marvin\Location\Domain\Repository\ZoneRepositoryInterface;
use Marvin\Location\Domain\ValueObject\ZoneType;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Domain\ValueObject\Label;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\ResetDatabase;

class DeleteZoneHandlerTest extends KernelTestCase
{
    use ResetDatabase;

    public function test_delete_leaf_zone_removes_it(): void
    {
        self::bootKernel();
        $c = static::getContainer();
        $bus = $c->get(SyncCommandBusInterface::class);
        /** @var ZoneRepositoryInterface $zones */
        $zones = $c->get(ZoneRepositoryInterface::class);

        $buildingId = $bus->handle(new CreateZone(new Label('Maison'), ZoneType::BUILDING));
        $leafId = $bus->handle(new CreateZone(new Label('Buanderie'), ZoneType::ROOM, parentZoneId: new ZoneId($buildingId)));

        $bus->handle(new DeleteZone(new ZoneId($leafId)));

        $this->expectExceptionMessageMatches('/Zone not found|Not Found/i');
        $zones->byId(new ZoneId($leafId));
    }

    public function test_delete_zone_with_children_throws(): void
    {
        self::bootKernel();
        $c = static::getContainer();
        $bus = $c->get(SyncCommandBusInterface::class);

        $buildingId = $bus->handle(new CreateZone(new Label('Maison'), ZoneType::BUILDING));
        $floorId = $bus->handle(new CreateZone(new Label('Etage1'), ZoneType::FLOOR, parentZoneId: new ZoneId($buildingId)));
        $bus->handle(new CreateZone(new Label('Chambre'), ZoneType::ROOM, parentZoneId: new ZoneId($floorId)));

        $this->expectException(InvalidZoneHierarchy::class);
        $bus->handle(new DeleteZone(new ZoneId($floorId)));
    }
}
