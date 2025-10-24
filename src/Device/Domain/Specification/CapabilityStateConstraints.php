<?php

namespace Marvin\Device\Domain\Specification;

use DateTimeInterface;
use Marvin\Device\Domain\Exception\CapabilityStateValidation;
use Marvin\Device\Domain\ValueObject\CapabilityStateDataType;
use Marvin\Shared\Domain\Specification\SpecificationInterface;

final readonly class CapabilityStateConstraints implements SpecificationInterface
{
    public function __construct(
        public CapabilityStateDataType $dataType,
        public int|float|null $min = null,
        public int|float|null $max = null,
        public ?array $allowedValues = null,
        public ?string $pattern = null,
        public ?string $unit = null,
        public ?int $precision = null,
    ) {
    }

    public function isSatisfiedBy(mixed $value): bool
    {
        return $this->isValid($value);
    }

    /**
     * Valide une valeur selon les contraintes
     *
     * @throws CapabilityStateValidation
     */
    private function validate(mixed $value): void
    {
        // 1. Validation du type
        $this->validateType($value);

        // 2. Validation des bornes (pour int/float)
        if ($this->dataType === CapabilityStateDataType::INTEGER || $this->dataType === CapabilityStateDataType::FLOAT) {
            $this->validateRange($value);
        }

        // 3. Validation des valeurs autorisées (pour enum/string)
        if ($this->allowedValues !== null && $this->dataType === CapabilityStateDataType::STRING) {
            $this->validateAllowedValues($value);
        }

        // 4. Validation du pattern (pour string)
        if ($this->pattern !== null && $this->dataType === CapabilityStateDataType::STRING) {
            $this->validatePattern($value);
        }

        // 5. Validation de la précision (pour float)
        if ($this->precision !== null && $this->dataType === CapabilityStateDataType::FLOAT) {
            $this->validatePrecision($value);
        }
    }

    private function validateType(mixed $value): void
    {
        $valid = match ($this->dataType) {
            CapabilityStateDataType::BOOLEAN => is_bool($value),
            CapabilityStateDataType::INTEGER => is_int($value),
            CapabilityStateDataType::FLOAT => is_float($value) || is_int($value), // int accepté pour float
            CapabilityStateDataType::STRING => is_string($value),
            CapabilityStateDataType::DATETIME => $value instanceof DateTimeInterface,
            CapabilityStateDataType::OBJECT, CapabilityStateDataType::ARRAY => is_array($value),
        };

        if (!$valid) {
            throw CapabilityStateValidation::withTypeAndDebugType(
                $this->dataType,
                get_debug_type($value)
            );
        }
    }

    private function validateRange(int|float $value): void
    {
        if ($this->min !== null && $value < $this->min) {
            throw CapabilityStateValidation::withMinAndValue($this->min, $value);
        }

        if ($this->max !== null && $value > $this->max) {
            throw CapabilityStateValidation::withMaxAndValue(
                $this->max,
                $value
            );
        }
    }

    private function validateAllowedValues(string $value): void
    {
        if (!in_array($value, $this->allowedValues, true)) {
            throw new CapabilityStateValidation(
                sprintf(
                    'Invalid value "%s". Allowed values: %s',
                    $value,
                    implode(', ', $this->allowedValues)
                )
            );
        }
    }

    private function validatePattern(string $value): void
    {
        if (!preg_match($this->pattern, $value)) {
            throw new CapabilityStateValidation(
                sprintf('Value "%s" does not match pattern %s', $value, $this->pattern)
            );
        }
    }

    private function validatePrecision(float $value): void
    {
        $decimals = strlen(substr(strrchr((string) $value, '.'), 1));
        if ($decimals > $this->precision) {
            throw new CapabilityStateValidation(
                sprintf(
                    'Value %s has %d decimals, maximum allowed is %d',
                    $value,
                    $decimals,
                    $this->precision
                )
            );
        }
    }

    /**
     * Normalise la valeur selon les contraintes
     * (arrondi, conversion, etc.)
     */
    public function normalize(mixed $value): mixed
    {
        // Conversion int → float si nécessaire
        if ($this->dataType === CapabilityStateDataType::FLOAT && is_int($value)) {
            $value = (float) $value;
        }

        // Arrondi selon la précision
        if ($this->dataType === CapabilityStateDataType::FLOAT && $this->precision !== null) {
            $value = round($value, $this->precision);
        }

        // Clamp entre min/max
        if ($this->dataType === CapabilityStateDataType::INTEGER || $this->dataType === CapabilityStateDataType::FLOAT) {
            if ($this->min !== null && $value < $this->min) {
                $value = $this->min;
            }
            if ($this->max !== null && $value > $this->max) {
                $value = $this->max;
            }
        }

        return $value;
    }

    /**
     * Vérifie si la valeur est valide (sans exception)
     */
    public function isValid(mixed $value): bool
    {
        try {
            $this->validate($value);
            return true;
        } catch (CapabilityStateValidation) {
            return false;
        }
    }
}
