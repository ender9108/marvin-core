<?php

namespace Marvin\System\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectInterface;
use Stringable;

final readonly class PluginStatus implements ValueObjectInterface
{
    public const array STATUSES = [
        'DISABLED' => 0,
        'ENABLED' => 1,
        'TO_DELETE' => 9,
    ];

    public int $value;

    public function __construct(string|int $status)
    {
        Assert::notEmpty($status);

        if (is_string($status)) {
            Assert::keyExists(self::STATUSES, $status);
            $status = self::STATUSES[$status];
        } else {
            Assert::inArray($status, self::STATUSES);
        }

        $this->value = $status;
    }

    public function equals(PluginStatus $status): bool
    {
        return $this->value === $status->value;
    }

    public function isDisabled(): bool
    {
        return $this->value === self::STATUSES['DISABLED'];
    }

    public function isEnabled(): bool
    {
        return $this->value === self::STATUSES['ENABLED'];
    }

    public function isToDelete(): bool
    {
        return $this->value === self::STATUSES['TO_DELETE'];
    }

    public static function disabled(): self
    {
        return new self(self::STATUSES['DISABLED']);
    }

    public static function enabled(): self
    {
        return new self(self::STATUSES['ENABLED']);
    }

    public static function toDelete(): self
    {
        return new self(self::STATUSES['TO_DELETE']);
    }
}
