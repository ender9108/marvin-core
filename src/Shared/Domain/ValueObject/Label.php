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

namespace Marvin\Shared\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Stringable;

final readonly class Label implements Stringable
{
    private const int MIN = 2;
    private const int MAX = 255;

    public string $value;

    public function __construct(string $label)
    {
        Assert::notEmpty($label, 'shared.exceptions.SH0011.label_does_not_empty');
        Assert::lengthBetween($label, self::MIN, self::MAX, 'shared.exceptions.SH0012.label_length_between');

        $this->value = $label;
    }

    public static function fromString(string $label): self
    {
        return new self($label);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
