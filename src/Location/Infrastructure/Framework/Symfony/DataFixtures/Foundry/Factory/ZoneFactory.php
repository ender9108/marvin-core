<?php

namespace Marvin\Location\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Factory;

use Marvin\Location\Domain\Model\Zone;
use Marvin\Location\Domain\ValueObject\Humidity;
use Marvin\Location\Domain\ValueObject\PowerConsumption;
use Marvin\Location\Domain\ValueObject\SurfaceArea;
use Marvin\Location\Domain\ValueObject\Temperature;
use Marvin\Location\Domain\ValueObject\ZoneName;
use Marvin\Location\Domain\ValueObject\ZoneType;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use ReflectionClass;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

final class ZoneFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Zone::class;
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        return [
            'id' => ZoneId::fromString(ZoneId::v7()),
            'zoneName' => ZoneName::fromString(self::faker()->words(2, true)),
            'type' => self::faker()->randomElement(ZoneType::cases()),
            'parent' => null,
            'surface' => null,
            'targetTemperature' => null,
            'icon' => null,
        ];
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this;
    }

    // ==========================================
    // PRESET METHODS (zones prédéfinies)
    // ==========================================

    /**
     * Crée une maison (building)
     */
    public function building(string $name = 'Maison'): self
    {
        return $this->with([
            'zoneName' => ZoneName::fromString($name),
            'type' => ZoneType::BUILDING,
            'icon' => '🏠',
        ]);
    }

    /**
     * Crée un étage (floor)
     */
    public function floor(string $name = 'Rez-de-chaussée'): self
    {
        return $this->with([
            'zoneName' => ZoneName::fromString($name),
            'type' => ZoneType::FLOOR,
            'icon' => '🏢',
        ]);
    }

    /**
     * Crée un salon
     */
    public function livingRoom(): self
    {
        return $this->with([
            'zoneName' => ZoneName::fromString('Salon'),
            'type' => ZoneType::ROOM,
            'surface' => SurfaceArea::fromFloat(25.0),
            'targetTemperature' => Temperature::fromCelsius(21.0),
            'icon' => '🛋️',
        ]);
    }

    /**
     * Crée une cuisine
     */
    public function kitchen(): self
    {
        return $this->with([
            'zoneName' => ZoneName::fromString('Cuisine'),
            'type' => ZoneType::ROOM,
            'surface' => SurfaceArea::fromFloat(15.0),
            'targetTemperature' => Temperature::fromCelsius(20.0),
            'icon' => '🍳',
        ]);
    }

    /**
     * Crée une chambre
     */
    public function bedroom(int $number = 1): self
    {
        return $this->with([
            'zoneName' => ZoneName::fromString("Chambre {$number}"),
            'type' => ZoneType::ROOM,
            'surface' => SurfaceArea::fromFloat(12.0),
            'targetTemperature' => Temperature::fromCelsius(19.0),
            'icon' => '🛏️',
        ]);
    }

    /**
     * Crée une salle de bain
     */
    public function bathroom(): self
    {
        return $this->with([
            'zoneName' => ZoneName::fromString('Salle de bain'),
            'type' => ZoneType::ROOM,
            'surface' => SurfaceArea::fromFloat(8.0),
            'targetTemperature' => Temperature::fromCelsius(22.0),
            'icon' => '🚿',
        ]);
    }

    /**
     * Crée un bureau
     */
    public function office(): self
    {
        return $this->with([
            'zoneName' => ZoneName::fromString('Bureau'),
            'type' => ZoneType::ROOM,
            'surface' => SurfaceArea::fromFloat(10.0),
            'targetTemperature' => Temperature::fromCelsius(20.0),
            'icon' => '💼',
        ]);
    }

    /**
     * Crée un jardin
     */
    public function garden(): self
    {
        return $this->with([
            'zoneName' => ZoneName::fromString('Jardin'),
            'type' => ZoneType::OUTDOOR,
            'surface' => SurfaceArea::fromFloat(50.0),
            'icon' => '🌳',
        ]);
    }

    /**
     * Crée un garage
     */
    public function garage(): self
    {
        return $this->with([
            'zoneName' => ZoneName::fromString('Garage'),
            'type' => ZoneType::OUTDOOR,
            'surface' => SurfaceArea::fromFloat(20.0),
            'icon' => '🚗',
        ]);
    }

    // ==========================================
    // WITH METHODS (ajout de données)
    // ==========================================

    /**
     * Ajoute une zone parente
     */
    public function withParent(Zone $parent): self
    {
        return $this->with([
            'parent' => $parent,
        ]);
    }

    /**
     * Ajoute une surface
     */
    public function withSurface(float $squareMeters): self
    {
        return $this->with([
            'surface' => SurfaceArea::fromFloat($squareMeters),
        ]);
    }

    /**
     * Ajoute une température cible
     */
    public function withTargetTemperature(float $celsius): self
    {
        return $this->with([
            'targetTemperature' => Temperature::fromCelsius($celsius),
        ]);
    }

    /**
     * Ajoute une température actuelle
     */
    public function withTemperature(float $celsius): self
    {
        return $this->afterInstantiate(function (Zone $zone) use ($celsius): void {
            // Utiliser la réflexion pour setter la température directement
            $reflection = new ReflectionClass($zone);
            $property = $reflection->getProperty('currentTemperature');
            $property->setValue($zone, Temperature::fromCelsius($celsius));
        });
    }

    /**
     * Ajoute une humidité actuelle
     */
    public function withHumidity(float $percentage): self
    {
        return $this->afterInstantiate(function (Zone $zone) use ($percentage): void {
            $reflection = new ReflectionClass($zone);
            $property = $reflection->getProperty('currentHumidity');
            $property->setValue($zone, Humidity::fromPercentage($percentage));
        });
    }

    /**
     * Ajoute une consommation électrique actuelle
     */
    public function withPowerConsumption(float $watts): self
    {
        return $this->afterInstantiate(function (Zone $zone) use ($watts): void {
            $reflection = new ReflectionClass($zone);
            $property = $reflection->getProperty('currentPowerConsumption');
            $property->setValue($zone, PowerConsumption::fromWatts($watts));
        });
    }

    /**
     * Marque la zone comme occupée
     */
    public function occupied(): self
    {
        return $this->afterInstantiate(function (Zone $zone): void {
            $zone->markAsOccupied();
        });
    }

    /**
     * Marque la zone comme inoccupée
     */
    public function unoccupied(): self
    {
        return $this->afterInstantiate(function (Zone $zone): void {
            // Incrémenter 3 fois le compteur no-motion
            $zone->incrementNoMotionCount();
            $zone->incrementNoMotionCount();
            $zone->incrementNoMotionCount();
        });
    }

    /**
     * Ajoute un device à la zone
     */
    public function withDevice(DeviceId|string $deviceId): self
    {
        if (is_string($deviceId)) {
            $deviceId = DeviceId::fromString($deviceId);
        }

        return $this->afterInstantiate(function (Zone $zone) use ($deviceId): void {
            $zone->addDevice($deviceId);
        });
    }

    /**
     * Ajoute plusieurs devices à la zone
     *
     * @param array<DeviceId|string> $deviceIds
     */
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

    /**
     * Ajoute un nombre spécifique de capteurs actifs
     * @throws \ReflectionException
     */
    public function withActiveSensors(int $count): self
    {
        return $this->afterInstantiate(function (Zone $zone) use ($count): void {
            $reflection = new ReflectionClass($zone);
            $property = $reflection->getProperty('activeSensorsCount');
            $property->setValue($zone, $count);
        });
    }

    // ==========================================
    // STATES METHODS (états complets)
    // ==========================================

    /**
     * Zone avec température et humidité confortables
     */
    public function comfortable(): self
    {
        return $this
            ->withTemperature(21.0)
            ->withHumidity(45.0)
            ->occupied();
    }

    /**
     * Zone surchauffée
     */
    public function overheated(): self
    {
        return $this
            ->withTargetTemperature(20.0)
            ->withTemperature(23.5);
    }

    /**
     * Zone sous-chauffée
     */
    public function underheated(): self
    {
        return $this
            ->withTargetTemperature(21.0)
            ->withTemperature(18.0);
    }

    /**
     * Zone avec forte consommation
     */
    public function highConsumption(): self
    {
        return $this
            ->withPowerConsumption(2500.0);
    }

    /**
     * Zone avec faible consommation
     */
    public function lowConsumption(): self
    {
        return $this
            ->withPowerConsumption(50.0);
    }

    /**
     * Zone avec humidité trop élevée
     */
    public function tooHumid(): self
    {
        return $this
            ->withHumidity(75.0);
    }

    /**
     * Zone avec humidité trop basse
     */
    public function tooDry(): self
    {
        return $this
            ->withHumidity(20.0);
    }

    /**
     * Zone complète avec toutes les métriques
     */
    public function withAllMetrics(): self
    {
        return $this
            ->withTemperature(21.5)
            ->withHumidity(50.0)
            ->withPowerConsumption(150.0)
            ->withActiveSensors(2)
            ->occupied();
    }
}
