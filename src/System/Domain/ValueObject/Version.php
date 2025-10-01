<?php
namespace Marvin\System\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert;
use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectInterface;
use Stringable;

final readonly class Version implements ValueObjectInterface, Stringable
{
    public const int MIN = 1;
    public const int MAX = 8;

    public string $value;

    public function __construct(string $version) {
        Assert::notEmpty($version);
        Assert::lengthBetween($version, self::MIN, self::MAX);

        $this->value = $version;
    }

    public function equals(Version $version): bool
    {
        return $this->value === $version->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
