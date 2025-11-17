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

final readonly class Slug implements Stringable
{
    public string $value;

    public function __construct(string $value)
    {
        Assert::notEmpty($value, 'SH0001::slug_does_not_empty');

        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
