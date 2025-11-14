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

namespace Marvin\Location\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Stringable;

final readonly class SurfaceArea implements Stringable
{
    public const float MIN_VALUE = 0.0;
    public const float MAX_VALUE = 100000.0;
    public const string UNIT = 'm²';

    private function __construct(
        public ?float $value,
    ) {
        if (null !== $this->value) {
            Assert::greaterThan($this->value, self::MIN_VALUE, 'location.exceptions.LO0020.zone_surface_area_cannot_be_negative');
            Assert::lessThan($this->value, self::MAX_VALUE, 'location.exceptions.LO0021.zone_surface_area_cannot_exceed_100000');
        }
    }

    public static function fromFloat(?float $value): self
    {
        return new self(null !== $value ? round($value, 2) : null);
    }

    public function __toString(): string
    {
        if ($this->value === null) {
            return '';
        }

        return number_format(
            $this->value,
            2
        ).' '.self::UNIT;
    }

    public function toFloat(): ?float
    {
        return $this->value;
    }
}
