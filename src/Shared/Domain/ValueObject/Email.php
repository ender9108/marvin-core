<?php

namespace Marvin\Shared\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectInterface;
use Override;
use Stringable;

final readonly class Email implements ValueObjectInterface, Stringable
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

    public function equals(Email $email): bool
    {
        return $this->value === $email->value;
    }

    #[Override]
    public function __toString(): string
    {
        return $this->value;
    }
}
