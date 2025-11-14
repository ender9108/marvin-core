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

namespace Marvin\Security\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectTrait;
use Stringable;

final readonly class Firstname implements Stringable
{
    use ValueObjectTrait;

    private const int MIN = 1;
    private const int MAX = 255;

    public string $value;

    public function __construct(string $firstname)
    {
        Assert::notEmpty($firstname, 'security.exceptions.SC0030.firstname_does_not_empty');
        Assert::lengthBetween(
            $firstname,
            self::MIN,
            self::MAX,
            'security.exceptions.SC0032.firstname_length_between'
        );

        $this->value = $firstname;
    }

    public static function fromString(string $firstname): self
    {
        return new self($firstname);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
