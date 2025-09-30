<?php
namespace Marvin\System\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert;
use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectInterface;
use Stringable;

final readonly class Command implements ValueObjectInterface, Stringable
{
    public string $value;

    public function __construct(string $command) {
        Assert::notEmpty($command);

        $this->value = $command;
    }

    public function equals(Command $command): bool
    {
        return $this->value === $command->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
