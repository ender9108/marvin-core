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
        Assert::notEmpty($email);
        Assert::email($email);
        Assert::lengthBetween($email, self::MIN, self::MAX);

        $this->value = $email;
    }

    #[Override]
    public function __toString(): string
    {
        return $this->value;
    }
}
