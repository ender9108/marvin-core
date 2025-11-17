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
use Override;
use Stringable;

final readonly class Email implements Stringable
{
    private const int MIN = 5;
    private const int MAX = 255;

    public string $value;

    public function __construct(string $email)
    {
        Assert::notEmpty($email, 'shared.exceptions.SH0008.email_does_not_empty');
        Assert::email($email, 'shared.exceptions.SH0009.email_is_not_valid');
        Assert::lengthBetween($email, self::MIN, self::MAX, 'shared.exceptions.SH0010.email_length_between');

        $this->value = $email;
    }

    public static function fromString(string $email): self
    {
        return new self($email);
    }

    #[Override]
    public function __toString(): string
    {
        return $this->value;
    }
}
