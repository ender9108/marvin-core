<?php

namespace MarvinTests\Location\Application\Command;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use Marvin\Location\Application\Command\Zone\CreateZone;
use Marvin\Location\Application\Command\Zone\UpdateZone;
use Marvin\Location\Domain\Repository\ZoneRepositoryInterface;
use Marvin\Location\Domain\ValueObject\HexaColor;
use Marvin\Location\Domain\ValueObject\Orientation;
use Marvin\Location\Domain\ValueObject\SurfaceArea;
use Marvin\Location\Domain\ValueObject\TargetPowerConsumption;
use Marvin\Location\Domain\ValueObject\TargetTemperature;
use Marvin\Location\Domain\ValueObject\ZoneType;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Domain\ValueObject\Label;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Foundry\Test\ResetDatabase;

class UpdateZoneHandlerTest extends KernelTestCase
{
    use ResetDatabase;

    public function test_update_zone_label_and_configuration(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var SyncCommandBusInterface $bus */
        $bus = $container->get(SyncCommandBusInterface::class);
        /** @var ZoneRepositoryInterface $zones */
        $zones = $container->get(ZoneRepositoryInterface::class);

        $zoneId = $bus->handle(new CreateZone(
            label: new Label('Cuisine'),
            type: ZoneType::ROOM,
        ));

        $updatedId = $bus->handle(new UpdateZone(
            zoneId: new ZoneId($zoneId),
            label: new Label('Cuisine ouverte'),
            surfaceArea: SurfaceArea::fromFloat(18.5),
            orientation: Orientation::EAST,
            targetTemperature: new TargetTemperature(20.0),
            targetPowerConsumption: new TargetPowerConsumption(750.0),
            icon: '🍳',
            color: new HexaColor('#ffcc00'),
        ));

        self::assertSame($zoneId, $updatedId);

        $zone = $zones->byId(new ZoneId($zoneId));
        self::assertSame('Cuisine ouverte', $zone->label?->value);
        self::assertSame(18.5, $zone->surfaceArea?->value);
        self::assertTrue($zone->orientation?->equals(Orientation::EAST));
        self::assertSame(20.0, $zone->targetTemperature?->value);
        self::assertSame(750.0, $zone->targetPowerConsumption?->value);
        self::assertSame('🍳', $zone->icon);
        self::assertSame('#ffcc00', $zone->color?->value);
    }
}
