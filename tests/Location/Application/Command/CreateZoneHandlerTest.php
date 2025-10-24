<?php

namespace MarvinTests\Location\Application\Command;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use Marvin\Location\Application\Command\Zone\CreateZone;
use Marvin\Location\Domain\Repository\ZoneRepositoryInterface;
use Marvin\Location\Domain\ValueObject\ZoneType;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Domain\ValueObject\Label;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\ResetDatabase;

class CreateZoneHandlerTest extends KernelTestCase
{
    use ResetDatabase;

    public function test_create_root_zone_persists_and_returns_id(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var SyncCommandBusInterface $bus */
        $bus = $container->get(SyncCommandBusInterface::class);
        /** @var ZoneRepositoryInterface $zones */
        $zones = $container->get(ZoneRepositoryInterface::class);

        $label = new Label('Maison');
        $id = $bus->handle(new CreateZone(
            label: $label,
            type: ZoneType::BUILDING,
        ));

        self::assertIsString($id);

        $zone = $zones->byId(new ZoneId($id));
        self::assertNotNull($zone);
        self::assertSame('Maison', $zone->label?->value);
        self::assertSame(ZoneType::BUILDING, $zone->type);
        self::assertNotNull($zone->path); // a root path should be set
    }
}
