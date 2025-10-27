<?php

namespace Marvin\Secret\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;
use Marvin\Secret\Domain\ValueObject\SecretKey;

class RotationError extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        string $code,
        public readonly bool $negativeInterval = false,
        public readonly bool $requireIntervalDayGtZero = false,
        public readonly bool $onlyAllowForManaged = false,
    ) {
        parent::__construct($message, $code);
    }

    public static function negativeInterval(): self {
        return new self(
            'The rotation interval cannot be negative',
            'S0006',
            true
        );
    }

    public static function requireIntervalDayGtZero(): self {
        return new self(
            'Auto-rotate requires a rotation interval > 0',
            'S0007',
            false,
            true
        );
    }

    public static function onlyAllowForManaged(): self {
        return new self(
            'Auto-rotation is only allowed for managed secrets',
            'S0008',
            false,
            false,
            true
        );
    }

    public function translationId(): string
    {
        if (true === $this->negativeInterval) {
            return 'secret.exceptions.rotation_error_negative_interval';
        }

        if (true === $this->requireIntervalDayGtZero) {
            return 'secret.exceptions.rotation_error_require_interval_day_gt_zero';
        }

        if (true === $this->onlyAllowForManaged) {
            return 'secret.exceptions.rotation_error_only_allow_for_managed';
        }

        return 'secret.exceptions.rotation_error';
    }

    public function translationParameters(): array
    {
        return [

        ];
    }

    public function translationDomain(): string
    {
        return 'secret';
    }
}
