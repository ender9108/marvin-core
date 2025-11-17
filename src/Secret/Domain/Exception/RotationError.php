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

namespace Marvin\Secret\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\Interfaces\TranslatableExceptionInterface;

class RotationError extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        public readonly bool $negativeInterval = false,
        public readonly bool $requireIntervalDayGtZero = false,
        public readonly bool $onlyAllowForManaged = false,
    ) {
        parent::__construct($message);
    }

    public static function negativeInterval(): self
    {
        return new self(
            'The rotation interval cannot be negative',
            true
        );
    }

    public static function requireIntervalDayGtZero(): self
    {
        return new self(
            'Auto-rotate requires a rotation interval > 0',
            false,
            true
        );
    }

    public static function onlyAllowForManaged(): self
    {
        return new self(
            'Auto-rotation is only allowed for managed secrets',
            false,
            false,
            true
        );
    }

    public function translationId(): string
    {
        if (true === $this->negativeInterval) {
            return 'secret.exceptions.SR0008.rotation_error_negative_interval';
        }

        if (true === $this->requireIntervalDayGtZero) {
            return 'secret.exceptions.SR0009.rotation_error_require_interval_day_gt_zero';
        }

        if (true === $this->onlyAllowForManaged) {
            return 'secret.exceptions.SR0010.rotation_error_only_allow_for_managed';
        }

        return 'secret.exceptions.SR0011.rotation_error';
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
