<?php

namespace Marvin\System\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;

final readonly class WorkerAllowedActions
{
    private const array ALLOWED_ACTIONS = ['start', 'stop', 'restart'];

    public array $value;

    public function __construct(array $value = [])
    {
        Assert::notEmpty($value);
        Assert::allInArray($value, self::ALLOWED_ACTIONS);

        $this->value = $value;
    }

    public function toArray(): array
    {
        return $this->value;
    }
}
