<?php

namespace Marvin\Location\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Factory;

use Marvin\Location\Domain\Model\Zone;
use Marvin\Location\Domain\ValueObject\HexaColor;
use Marvin\Location\Domain\ValueObject\Orientation;
use Marvin\Location\Domain\ValueObject\SurfaceArea;
use Marvin\Location\Domain\ValueObject\TargetPowerConsumption;
use Marvin\Location\Domain\ValueObject\TargetTemperature;
use Marvin\Location\Domain\ValueObject\ZonePath;
use Marvin\Location\Domain\ValueObject\ZoneType;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Metadata;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class ZoneFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Zone::class;
    }

    protected function defaults(): array|callable
    {
        $name = self::faker()->unique()->word();

        return [
            'label' => new Label($name),
            'type' => self::faker()->randomElement(ZoneType::cases())->value,
            'parentZoneId' => null,
            'surfaceArea' => self::faker()->boolean(70) ? SurfaceArea::fromFloat(self::faker()->randomFloat(2, 5, 50)) : null,
            'orientation' => self::faker()->boolean(60) ? self::faker()->randomElement(Orientation::cases()) : null,
            'targetTemperature' => self::faker()->boolean(70) ? new TargetTemperature(self::faker()->randomFloat(1, 18, 24)) : null,
            'targetPowerConsumption' => self::faker()->boolean(50) ? new TargetPowerConsumption(self::faker()->randomFloat(2, 100, 3000)) : null,
            'icon' => self::faker()->randomElement(['🏠', '🛋️', '🛏️', '🍳', '🚿', '🌳', '🚗', null]),
            'color' => self::faker()->boolean(70) ? new HexaColor(self::faker()->hexColor()) : null,
            'metadata' => new Metadata([]),
        ];
    }

    protected function initialize(): static
    {
        return $this->afterInstantiate(function (Zone $zone) {
            if ($zone->parentZoneId === null) {
                $zone->updatePath(ZonePath::fromString($zone->label->value));
            }
        });
    }

    // États nommés
    public function building(): self
    {
        return $this->with([
            'type' => ZoneType::BUILDING,
            'icon' => '🏠',
            'surfaceArea' => SurfaceArea::fromFloat(self::faker()->randomFloat(2, 80, 250)),
        ]);
    }

    public function room(): self
    {
        return $this->with([
            'type' => ZoneType::ROOM,
            'targetTemperature' => new TargetTemperature(self::faker()->randomFloat(1, 19, 22)),
        ]);
    }

    public function outdoor(): self
    {
        return $this->with([
            'name' => new Label('maison'),
            'type' => ZoneType::OUTDOOR,
            'icon' => '🌳',
            'targetTemperature' => null,
        ]);
    }

    public function livingRoom(): self
    {
        return $this->with([
            'name' => new Label('salon'),
            'type' => ZoneType::ROOM,
            'surfaceArea' => SurfaceArea::fromFloat(25.0),
            'targetTemperature' => new TargetTemperature(21.0),
            'icon' => '🛋️',
            'color' => new HexaColor('#FFA500'),
        ]);
    }

    public function bedroom(): self
    {
        return $this->with([
            'name' => new Label('chambre'),
            'type' => ZoneType::ROOM,
            'surfaceArea' => SurfaceArea::fromFloat(15.0),
            'targetTemperature' => new TargetTemperature(19.0),
            'icon' => '🛏️',
            'color' => new HexaColor('#4169E1'),
        ]);
    }

    public function kitchen(): self
    {
        return $this->with([
            'name' => new Label('cuisine'),
            'type' => ZoneType::ROOM,
            'surfaceArea' => SurfaceArea::fromFloat(12.0),
            'targetTemperature' => new TargetTemperature(20.0),
            'icon' => '🍳',
            'color' => new HexaColor('#FF6347'),
        ]);
    }

    public function bathroom(): self
    {
        return $this->with([
            'name' => new Label('salle_de_bain'),
            'type' => ZoneType::ROOM,
            'surfaceArea' => SurfaceArea::fromFloat(8.0),
            'targetTemperature' => new TargetTemperature(22.0),
            'icon' => '🚿',
            'color' => new HexaColor('#87CEEB'),
        ]);
    }

    public function garden(): self
    {
        return $this->with([
            'name' => new Label('jardin'),
            'type' => ZoneType::OUTDOOR,
            'surfaceArea' => SurfaceArea::fromFloat(50.0),
            'icon' => '🌳',
            'color' => new HexaColor('#228B22'),
        ]);
    }

    public function garage(): self
    {
        return $this->with([
            'name' => new Label('garage'),
            'type' => ZoneType::OUTDOOR,
            'surfaceArea' => SurfaceArea::fromFloat(20.0),
            'icon' => '🚗',
            'color' => new HexaColor('#696969'),
        ]);
    }

    public function occupied(): self
    {
        return $this->afterInstantiate(function (Zone $zone) {
            $zone->markAsOccupied();
        });
    }

    public function unoccupied(): self
    {
        return $this->afterInstantiate(function (Zone $zone) {
            $zone->incrementNoMotionCount();
            $zone->incrementNoMotionCount();
            $zone->incrementNoMotionCount();
        });
    }

    public function withTemperature(float $temperature): self
    {
        return $this->afterInstantiate(function (Zone $zone) use ($temperature) {
            $zone->updateAverageTemperature($temperature);
        });
    }

    public function withPowerConsumption(float $consumption): self
    {
        return $this->afterInstantiate(function (Zone $zone) use ($consumption) {
            $zone->updatePowerConsumption($consumption);
        });
    }

    public function hot(): self
    {
        return $this->with(['targetTemperature' => 20.0])
            ->afterInstantiate(function (Zone $zone) {
                $zone->updateAverageTemperature(26.0);
            });
    }

    public function cold(): self
    {
        return $this->with(['targetTemperature' => 20.0])
            ->afterInstantiate(function (Zone $zone) {
                $zone->updateAverageTemperature(16.0);
            });
    }

    public function overBudget(): self
    {
        return $this->with(['targetPowerConsumption' => 1000.0])
            ->afterInstantiate(function (Zone $zone) {
                $zone->updatePowerConsumption(1500.0);
            });
    }

    public function withParent(Zone|string $parent): self
    {
        $parentId = $parent instanceof Zone ? $parent->id : ZoneId::fromString($parent);

        return $this->afterInstantiate(function (Zone $zone) use ($parent, $parentId) {
            $zone->moveToParent($parentId);

            if ($parent instanceof Zone) {
                $newPath = $parent->path->append($zone->label->value);
                $zone->updatePath($newPath);
            }
        });
    }
}
