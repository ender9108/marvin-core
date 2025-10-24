<?php

namespace Marvin\Device\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;
use Marvin\Device\Domain\ValueObject\Capability;
use Marvin\Device\Domain\ValueObject\CapabilityStateDataType;
use Marvin\Shared\Domain\ValueObject\Label;

class CapabilityStateValidation extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        string $code,
        private readonly mixed $value = null,
        private readonly ?string $stateDataType = null,
        private readonly ?string $debugType = null,
        private readonly ?int $min = null,
        private readonly ?int $max = null,
    ) {
        parent::__construct($message, $code);
    }

    public static function withTypeAndDebugType(
        CapabilityStateDataType $stateDataType,
        string $debugType,
    ): self {
        return new self(
            sprintf(
                'Invalid type: expected %s, got %s',
                $stateDataType->value,
                $debugType
            ),
            '',
            null,
            $stateDataType->value,
            $debugType,
            null,
            null,
        );
    }

    public static function withMinAndValue(int $min, mixed $value): self {
        return new self(
            sprintf('Value %s is below minimum %s', $value, $min),
            '', $value, null, null, $min, null
        );
    }

    public static function withMaxAndValue(int $max, mixed $value): self {
        return new self(
            sprintf('Value %s exceeds maximum %s', $value, $max),
            '', $value, null, null, null, $max
        );
    }

    public function translationId(): string
    {
        if (null !== $this->stateDataType && null !== $this->debugType) {
            return 'device.exceptions.capability_state_validation_type';
        }

        if (null !== $this->min && null !== $this->value) {
            return 'device.exceptions.capability_state_validation_min';
        }

        if (null !== $this->max && null !== $this->value) {
            return 'device.exceptions.capability_state_validation_max';
        }

        return 'device.exceptions.capability_state_validation';
    }

    public function translationParameters(): array
    {
        return [
            '%state_data_type%' => $this->stateDataType,
            '%debug_type%' => $this->debugType,
            '%min%' => $this->min,
            '%max%' => $this->max,
            '%value%' => $this->value,
        ];
    }

    public function translationDomain(): string
    {
        return 'device';
    }
}
