<?php
namespace Marvin\Shared\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert;
use Override;
use Stringable;

final class Email implements Stringable
{
    private const int MIN = 5;
    private const int MAX = 255;

    public readonly string $email;

    public function __construct(string $email) {
        Assert::notEmpty($email);
        Assert::email($email);
        Assert::lengthBetween($email, self::MIN, self::MAX);

        $this->email = $email;
    }

    public function equals(Email $email): bool
    {
        return $this->email === $email->email;
    }

    #[Override]
    public function __toString(): string
    {
        return $this->email;
    }
}
