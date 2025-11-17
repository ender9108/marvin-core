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

namespace Marvin\Device\Domain\Specification;

use DateTimeInterface;
use InvalidArgumentException;
use Marvin\Device\Domain\ValueObject\CapabilityStateDataType;

/**
 * CapabilityStateConstraints - Contraintes de validation pour les états de capabilities
 *
 * Définit les règles de validation pour les valeurs d'états
 */
final readonly class CapabilityStateConstraints
{
    /**
     * @param CapabilityStateDataType $dataType Type de données attendu
     * @param float|int|null $min Valeur minimale (pour INTEGER/FLOAT)
     * @param float|int|null $max Valeur maximale (pour INTEGER/FLOAT)
     * @param string|null $unit Unité de mesure (ex: "°C", "%", "lux")
     * @param int|null $precision Nombre de décimales (pour FLOAT)
     * @param array<int, mixed>|null $allowedValues Valeurs autorisées (pour enum-like)
     * @param string|null $pattern Pattern regex pour validation (pour STRING)
     */
    public function __construct(
        public CapabilityStateDataType $dataType,
        public float|int|null $min = null,
        public float|int|null $max = null,
        public ?string $unit = null,
        public ?int $precision = null,
        public ?array $allowedValues = null,
        public ?string $pattern = null,
    ) {
    }

    /**
     * Valide une valeur selon les contraintes
     *
     * @throws InvalidArgumentException Si la valeur ne respecte pas les contraintes
     */
    public function validate(mixed $value): void
    {
        // Validation du type de données
        $this->validateDataType($value);

        // Validation des contraintes spécifiques
        if ($this->min !== null && is_numeric($value) && $value < $this->min) {
            throw new InvalidArgumentException(sprintf(
                'Value %s is below minimum %s',
                $value,
                $this->min
            ));
        }

        if ($this->max !== null && is_numeric($value) && $value > $this->max) {
            throw new InvalidArgumentException(sprintf(
                'Value %s is above maximum %s',
                $value,
                $this->max
            ));
        }

        if ($this->allowedValues !== null && !in_array($value, $this->allowedValues, true)) {
            throw new InvalidArgumentException(sprintf(
                'Value "%s" is not in allowed values: %s',
                $value,
                implode(', ', array_map(fn ($v) => '"' . $v . '"', $this->allowedValues))
            ));
        }

        if ($this->pattern !== null && is_string($value) && !preg_match($this->pattern, $value)) {
            throw new InvalidArgumentException(sprintf(
                'Value "%s" does not match pattern %s',
                $value,
                $this->pattern
            ));
        }
    }

    /**
     * Valide le type de données
     */
    private function validateDataType(mixed $value): void
    {
        $isValid = match ($this->dataType) {
            CapabilityStateDataType::BOOLEAN => is_bool($value),
            CapabilityStateDataType::INTEGER => is_int($value),
            CapabilityStateDataType::FLOAT => is_float($value) || is_int($value), // int acceptable pour float
            CapabilityStateDataType::STRING => is_string($value),
            CapabilityStateDataType::DATETIME => $value instanceof DateTimeInterface || is_string($value),
            CapabilityStateDataType::OBJECT => is_array($value) && $this->isAssociativeArray($value),
            CapabilityStateDataType::ARRAY => is_array($value),
        };

        if (!$isValid) {
            throw new InvalidArgumentException(sprintf(
                'Value type "%s" does not match expected type "%s"',
                gettype($value),
                $this->dataType->value
            ));
        }
    }

    /**
     * Vérifie si un tableau est associatif (object-like)
     */
    private function isAssociativeArray(array $value): bool
    {
        if ($value === []) {
            return false;
        }

        return array_keys($value) !== range(0, count($value) - 1);
    }
}
