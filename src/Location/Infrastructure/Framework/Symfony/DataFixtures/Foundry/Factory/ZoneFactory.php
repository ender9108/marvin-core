<?php

namespace Marvin\Location\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Factory;

use Marvin\Location\Domain\Model\Zone;
use Marvin\Location\Domain\ValueObject\HexaColor;
use Marvin\Location\Domain\ValueObject\Humidity;
use Marvin\Location\Domain\ValueObject\Orientation;
use Marvin\Location\Domain\ValueObject\PowerConsumption;
use Marvin\Location\Domain\ValueObject\SurfaceArea;
use Marvin\Location\Domain\ValueObject\Temperature;
use Marvin\Location\Domain\ValueObject\ZoneName;
use Marvin\Location\Domain\ValueObject\ZoneType;
use Marvin\Shared\Domain\Service\SluggerInterface;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Domain\ValueObject\Metadata;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

final class ZoneFactory extends PersistentProxyObjectFactory
{
    public function __construct(
        private readonly SluggerInterface $slugger,
    ) {
        parent::__construct();
    }

    public static function class(): string
    {
        return Zone::class;
    }

    protected function defaults(): array
    {
        return [
            'zoneName' => self::faker()->words(2, true),
            'type' => self::faker()->randomElement(['building', 'floor', 'room', 'outdoor']),
            'id' => null,
            'targetTemperature' => null,
            'targetPowerConsumption' => null,
            'targetHumidity' => null,
            'icon' => null,
            'surfaceArea' => null,
            'orientation' => null,
            'color' => null,
            'metadata' => null,
        ];
    }

    protected function initialize(): static
    {
        return $this
            ->instantiateWith(function (array $attributes): Zone {
                // Conversion des valeurs brutes en Value Objects
                $zoneName = $attributes['zoneName'] instanceof ZoneName
                    ? $attributes['zoneName']
                    : ZoneName::fromString($attributes['zoneName']);

                $type = $attributes['type'] instanceof ZoneType
                    ? $attributes['type']
                    : ZoneType::from($attributes['type']);

                $id = $attributes['id'];
                if (is_string($id)) {
                    $id = ZoneId::fromString($id);
                }

                $targetTemperature = $attributes['targetTemperature'];
                if (is_float($targetTemperature) || is_int($targetTemperature)) {
                    $targetTemperature = Temperature::fromCelsius((float) $targetTemperature);
                }

                $targetPowerConsumption = $attributes['targetPowerConsumption'];
                if (is_float($targetPowerConsumption) || is_int($targetPowerConsumption)) {
                    $targetPowerConsumption = PowerConsumption::fromWatts((float) $targetPowerConsumption);
                }

                $targetHumidity = $attributes['targetHumidity'];
                if (is_float($targetHumidity) || is_int($targetHumidity)) {
                    $targetHumidity = Humidity::fromPercentage((float) $targetHumidity);
                }

                $surfaceArea = $attributes['surfaceArea'];
                if (is_float($surfaceArea) || is_int($surfaceArea)) {
                    $surfaceArea = new SurfaceArea((float) $surfaceArea);
                }

                $orientation = $attributes['orientation'];
                if (is_string($orientation)) {
                    $orientation = Orientation::from($orientation);
                }

                $color = $attributes['color'];
                if (is_string($color)) {
                    $color = new HexaColor($color);
                }

                $metadata = $attributes['metadata'];
                if (is_array($metadata)) {
                    $metadata = new Metadata($metadata);
                }

                $zone = new Zone(
                    zoneName: $zoneName,
                    type: $type,
                    id: $id,
                    targetTemperature: $targetTemperature,
                    targetPowerConsumption: $targetPowerConsumption,
                    targetHumidity: $targetHumidity,
                    icon: $attributes['icon'],
                    surfaceArea: $surfaceArea,
                    orientation: $orientation,
                    color: $color,
                    metadata: $metadata,
                );

                $zone->updateSlug($this->slugger);

                return $zone;
            });
    }

    // ==========================================
    // PRESET METHODS (zones prédéfinies)
    // ==========================================

    public function building(string $name = 'Maison'): self
    {
        return $this->with([
            'zoneName' => $name,
            'type' => 'building',
            'icon' => '🏠',
        ]);
    }

    public function floor(string $name = 'Rez-de-chaussée'): self
    {
        return $this->with([
            'zoneName' => $name,
            'type' => 'floor',
            'icon' => '🏢',
        ]);
    }

    public function livingRoom(): self
    {
        return $this->with([
            'zoneName' => 'Salon',
            'type' => 'room',
            'surfaceArea' => 25.0,
            'targetTemperature' => 21.0,
            'icon' => '🛋️',
        ]);
    }

    public function kitchen(): self
    {
        return $this->with([
            'zoneName' => 'Cuisine',
            'type' => 'room',
            'surfaceArea' => 15.0,
            'targetTemperature' => 20.0,
            'icon' => '🍳',
        ]);
    }

    public function bedroom(int $number = 1): self
    {
        return $this->with([
            'zoneName' => "Chambre {$number}",
            'type' => 'room',
            'surfaceArea' => 12.0,
            'targetTemperature' => 19.0,
            'icon' => '🛏️',
        ]);
    }

    public function bathroom(): self
    {
        return $this->with([
            'zoneName' => 'Salle de bain',
            'type' => 'room',
            'surfaceArea' => 8.0,
            'targetTemperature' => 22.0,
            'targetHumidity' => 60.0,
            'icon' => '🚿',
        ]);
    }

    public function office(): self
    {
        return $this->with([
            'zoneName' => 'Bureau',
            'type' => 'room',
            'surfaceArea' => 10.0,
            'targetTemperature' => 20.0,
            'icon' => '💼',
        ]);
    }

    public function garden(): self
    {
        return $this->with([
            'zoneName' => 'Jardin',
            'type' => 'outdoor',
            'surfaceArea' => 50.0,
            'icon' => '🌳',
        ]);
    }

    public function garage(): self
    {
        return $this->with([
            'zoneName' => 'Garage',
            'type' => 'outdoor',
            'surfaceArea' => 20.0,
            'icon' => '🚗',
        ]);
    }

    // ==========================================
    // WITH METHODS (configuration)
    // ==========================================

    public function withParent(Zone $parent): self
    {
        return $this->afterInstantiate(function (Zone $zone) use ($parent): void {
            // Obtenir l'objet réel si c'est un proxy Foundry
            $realParent = method_exists($parent, '_real') ? $parent->_real() : $parent;
            $zone->move($realParent);
        });
    }

    public function withSlug(): self
    {
        return $this->afterInstantiate(function (Zone $zone): void {
            $zone->updateSlug($this->slugger);
        });
    }

    public function withSurface(float $squareMeters): self
    {
        return $this->with([
            'surfaceArea' => $squareMeters,
        ]);
    }

    public function withTargetTemperature(float $celsius): self
    {
        return $this->with([
            'targetTemperature' => $celsius,
        ]);
    }

    public function withTargetHumidity(float $percentage): self
    {
        return $this->with([
            'targetHumidity' => $percentage,
        ]);
    }

    public function withTargetPowerConsumption(float $watts): self
    {
        return $this->with([
            'targetPowerConsumption' => $watts,
        ]);
    }

    public function withOrientation(string $orientation): self
    {
        return $this->with([
            'orientation' => $orientation, // 'north', 'south', 'east', 'west'
        ]);
    }

    public function withColor(string $hexColor): self
    {
        return $this->with([
            'color' => $hexColor, // '#FF5733'
        ]);
    }

    public function withIcon(string $icon): self
    {
        return $this->with([
            'icon' => $icon,
        ]);
    }

    public function withMetadata(array $metadata): self
    {
        return $this->with([
            'metadata' => $metadata,
        ]);
    }

    // ==========================================
    // WITH METHODS (métriques actuelles)
    // ==========================================

    public function withTemperature(float $celsius): self
    {
        return $this->afterInstantiate(function (Zone $zone) use ($celsius): void {
            $reflection = new \ReflectionClass($zone);
            $property = $reflection->getProperty('currentTemperature');
            $property->setValue($zone, Temperature::fromCelsius($celsius));
        });
    }

    public function withHumidity(float $percentage): self
    {
        return $this->afterInstantiate(function (Zone $zone) use ($percentage): void {
            $reflection = new \ReflectionClass($zone);
            $property = $reflection->getProperty('currentHumidity');
            $property->setValue($zone, Humidity::fromPercentage($percentage));
        });
    }

    public function withPowerConsumption(float $watts): self
    {
        return $this->afterInstantiate(function (Zone $zone) use ($watts): void {
            $reflection = new \ReflectionClass($zone);
            $property = $reflection->getProperty('currentPowerConsumption');
            $property->setValue($zone, PowerConsumption::fromWatts($watts));
        });
    }

    public function withActiveSensors(int $count): self
    {
        return $this->afterInstantiate(function (Zone $zone) use ($count): void {
            $reflection = new \ReflectionClass($zone);
            $property = $reflection->getProperty('activeSensorsCount');
            $property->setValue($zone, $count);
        });
    }

    // ==========================================
    // WITH METHODS (occupation)
    // ==========================================

    public function occupied(): self
    {
        return $this->afterInstantiate(function (Zone $zone): void {
            $zone->markAsOccupied();
        });
    }

    public function unoccupied(): self
    {
        return $this->afterInstantiate(function (Zone $zone): void {
            $zone->incrementNoMotionCount();
            $zone->incrementNoMotionCount();
            $zone->incrementNoMotionCount();
        });
    }

    // ==========================================
    // WITH METHODS (devices)
    // ==========================================

    public function withDevice(DeviceId|string $deviceId): self
    {
        if (is_string($deviceId)) {
            $deviceId = DeviceId::fromString($deviceId);
        }

        return $this->afterInstantiate(function (Zone $zone) use ($deviceId): void {
            $zone->addDevice($deviceId);
        });
    }

    public function withDevices(array $deviceIds): self
    {
        return $this->afterInstantiate(function (Zone $zone) use ($deviceIds): void {
            foreach ($deviceIds as $deviceId) {
                if (is_string($deviceId)) {
                    $deviceId = DeviceId::fromString($deviceId);
                }
                $zone->addDevice($deviceId);
            }
        });
    }

    // ==========================================
    // STATES METHODS (états complets)
    // ==========================================

    public function comfortable(): self
    {
        return $this
            ->withTemperature(21.0)
            ->withHumidity(45.0)
            ->occupied();
    }

    public function overheated(): self
    {
        return $this
            ->withTargetTemperature(20.0)
            ->withTemperature(23.5);
    }

    public function underheated(): self
    {
        return $this
            ->withTargetTemperature(21.0)
            ->withTemperature(18.0);
    }

    public function highConsumption(): self
    {
        return $this->withPowerConsumption(2500.0);
    }

    public function lowConsumption(): self
    {
        return $this->withPowerConsumption(50.0);
    }

    public function tooHumid(): self
    {
        return $this
            ->withHumidity(75.0)
            ->withTargetHumidity(60.0);
    }

    public function tooDry(): self
    {
        return $this
            ->withHumidity(20.0)
            ->withTargetHumidity(45.0);
    }

    public function withAllMetrics(): self
    {
        return $this
            ->withTemperature(21.5)
            ->withHumidity(50.0)
            ->withPowerConsumption(150.0)
            ->withActiveSensors(2)
            ->occupied();
    }

    public function complete(): self
    {
        return $this
            ->withSurface(25.0)
            ->withTargetTemperature(21.0)
            ->withTargetHumidity(50.0)
            ->withOrientation('south')
            ->withColor('#3B82F6')
            ->withAllMetrics();
    }
}







