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

namespace Marvin\Device\Domain\Model;

use DateTimeImmutable;
use InvalidArgumentException;
use Marvin\Device\Domain\ValueObject\Capability;
use Marvin\Device\Domain\ValueObject\CapabilityState;
use Marvin\Device\Domain\ValueObject\Identity\DeviceCapabilityId;
use Marvin\Shared\Domain\ValueObject\Metadata;
use Throwable;
use ValueError;

/**
 * DeviceCapability - Entity représentant une capability d'un device
 *
 * Part of Device aggregate, gère l'état actuel et l'historique d'une capability
 */
final class DeviceCapability
{
    public private(set) ?Device $device = null;

    private function __construct(
        private(set) Capability $capability,
        private(set) string $stateName,
        private(set) mixed $currentValue,
        private(set) ?DateTimeImmutable $lastUpdatedAt,
        private(set) ?Metadata $metadata = null,
        private(set) DeviceCapabilityId $id = new DeviceCapabilityId(),
    ) {
    }

    public static function create(
        Capability $capability,
        ?string $stateName = null,
        mixed $initialValue = null,
        array $metadata = [],
    ): self {
        // Si stateName n'est pas fourni, utiliser le premier state de la capability
        if ($stateName === null) {
            $states = CapabilityState::getStatesForCapability($capability);
            if (empty($states)) {
                throw new InvalidArgumentException(sprintf(
                    'No states defined for capability "%s"',
                    $capability->value
                ));
            }
            $stateName = $states[0]->value;
        }

        // Valider que le stateName appartient bien à cette capability
        $allowedStates = CapabilityState::getStatesForCapability($capability);
        $stateExists = array_any(
            $allowedStates,
            fn ($s) => $s->value === $stateName
        );

        if (!$stateExists) {
            throw new InvalidArgumentException(sprintf(
                'State "%s" is not valid for capability "%s"',
                $stateName,
                $capability->value
            ));
        }

        // Valider la valeur initiale si fournie
        if ($initialValue !== null) {
            self::validateValueForState($stateName, $initialValue);
        }

        return new self(
            capability: $capability,
            stateName: $stateName,
            currentValue: $initialValue,
            lastUpdatedAt: $initialValue !== null ? new DateTimeImmutable() : null,
            metadata: Metadata::fromArray($metadata),
        );
    }

    public function setDevice(?Device $device): void
    {
        $this->device = $device;
    }

    /**
     * Met à jour la valeur de la capability
     *
     * Valide la valeur selon le type de données et les contraintes du CapabilityState
     *
     * @throws InvalidArgumentException Si la valeur ne respecte pas les contraintes
     */
    public function updateValue(mixed $newValue): void
    {
        // Validation de la valeur selon les contraintes du state
        self::validateValueForState($this->stateName, $newValue);

        $this->currentValue = $newValue;
        $this->lastUpdatedAt = new DateTimeImmutable();
    }

    /**
     * Valide une valeur pour un state donné (méthode statique)
     *
     * @throws InvalidArgumentException Si la valeur ne respecte pas les contraintes
     */
    private static function validateValueForState(string $stateName, mixed $value): void
    {
        try {
            $capabilityState = CapabilityState::from($stateName);
            $constraints = $capabilityState->getConstraints();
            $constraints->validate($value);
        } catch (ValueError) {
            // State inconnu, on accepte la valeur (pour compatibilité)
            return;
        } catch (Throwable $e) {
            throw new InvalidArgumentException(sprintf(
                'Invalid value for state "%s": %s',
                $stateName,
                $e->getMessage()
            ), 0, $e);
        }
    }

    /**
     * Définit l'unité de mesure pour cette capability
     *
     * Utilisé pour stocker l'unité (ex: "°C", "%", "lux") dans les metadata
     *
     * @param string $unit Unité de mesure (ex: "°C", "%", "lux")
     */
    public function setUnit(string $unit): void
    {
        if ($this->metadata === null) {
            $this->metadata = Metadata::fromArray(['unit' => $unit]);
        } else {
            $this->metadata = $this->metadata->with('unit', $unit);
        }
    }

    /**
     * Vérifie si la capability est en lecture seule
     */
    public function isReadOnly(): bool
    {
        return $this->capability->isReadOnly();
    }

    /**
     * Retourne l'état actuel sous forme de tableau
     *
     * @return array<string, mixed>
     */
    public function toStateArray(): array
    {
        if ($this->currentValue === null) {
            return [];
        }

        return [$this->stateName => $this->currentValue];
    }
}
