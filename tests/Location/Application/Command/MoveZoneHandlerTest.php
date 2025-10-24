<?php

namespace MarvinTests\Location\Application\Command;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use Marvin\Location\Application\Command\Zone\CreateZone;
use Marvin\Location\Application\Command\Zone\MoveZone;
use Marvin\Location\Domain\Exception\InvalidZoneHierarchy;
use Marvin\Location\Domain\Repository\ZoneRepositoryInterface;
use Marvin\Location\Domain\ValueObject\ZoneType;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Domain\ValueObject\Label;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\ResetDatabase;

class MoveZoneHandlerTest extends KernelTestCase
{
    use ResetDatabase;

    public function test_move_room_under_building_updates_parent_and_path(): void
    {
        self::bootKernel();
        $c = static::getContainer();
        $bus = $c->get(SyncCommandBusInterface::class);
        /** @var ZoneRepositoryInterface $zones */
        $zones = $c->get(ZoneRepositoryInterface::class);

        $buildingId = $bus->handle(new CreateZone(new Label('Maison'), ZoneType::BUILDING));
        $floorId = $bus->handle(new CreateZone(new Label('Etage1'), ZoneType::FLOOR, parentZoneId: new ZoneId($buildingId)));
        $roomId = $bus->handle(new CreateZone(new Label('Salon'), ZoneType::ROOM, parentZoneId: new ZoneId($floorId)));

        // Move room directly under building
        $bus->handle(new MoveZone(new ZoneId($roomId), new ZoneId($buildingId)));

        $room = $zones->byId(new ZoneId($roomId));
        $parent = $zones->byId(new ZoneId($buildingId));
        self::assertTrue($room->parent?->id->equals(new ZoneId($buildingId)));
        self::assertNotNull($room->path);
        self::assertTrue($room->path->isChildOf($parent->path));
    }

    public function test_move_under_room_throws_invalid_hierarchy(): void
    {
        self::bootKernel();
        $c = static::getContainer();
        $bus = $c->get(SyncCommandBusInterface::class);

        $buildingId = $bus->handle(new CreateZone(new Label('Maison'), ZoneType::BUILDING));
        $roomParentId = $bus->handle(new CreateZone(new Label('Salon'), ZoneType::ROOM, parentZoneId: new ZoneId($buildingId)));
        $roomChildId = $bus->handle(new CreateZone(new Label('Canape'), ZoneType::ROOM, parentZoneId: new ZoneId($buildingId)));

        $this->expectException(InvalidZoneHierarchy::class);
        // Try to move a zone under a ROOM (rooms cannot have children)
        $bus->handle(new MoveZone(new ZoneId($roomChildId), new ZoneId($roomParentId)));
    }
}
