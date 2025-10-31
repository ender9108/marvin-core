<?php

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

    #[Override]
    public function __toString(): string
    {
        return $this->value;
    }
}
