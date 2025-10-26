<?php

namespace Marvin\System\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectInterface;
use Marvin\Shared\Domain\ValueObject\ArrayValueObjectInterface;

final readonly class WorkerAllowedActions implements ArrayValueObjectInterface
{
    private const array ALLOWED_ACTIONS = ['start', 'stop', 'restart'];

    public array $value;

    public function __construct(array $value = [])
    {
        Assert::notEmpty($value);
        Assert::allInArray($value, self::ALLOWED_ACTIONS);

        $this->value = $value;
    }

    public function equals(ValueObjectInterface $other): bool
    {
        return $other instanceof self && $this->value === $other->value;
    }

    public function toArray(): array
    {
        return $this->value;
    }
}
