<?php

namespace Marvin\System\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;

final readonly class WorkerAllowedActions
{
    private const array ALLOWED_ACTIONS = ['start', 'stop', 'restart'];

    public array $value;

    public function __construct(array $value = [])
    {
        Assert::notEmpty($value, 'system.exceptions.SY0018.worker_actions_does_not_empty');;
        Assert::allInArray($value, self::ALLOWED_ACTIONS, 'system.exceptions.SY0019.worker_actions_is_not_available');

        $this->value = $value;
    }

    public function toArray(): array
    {
        return $this->value;
    }
}
