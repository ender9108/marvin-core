<?php

namespace Marvin\System\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\MarvinManagerBundle\Reference\ManagerContainerActionReference;

final readonly class ContainerAllowedActions
{
    public array $value;

    public function __construct(array $value = [])
    {
        Assert::notEmpty($value, 'system.exceptions.SY0016.container_actions_does_not_empty');
        Assert::allInArray($value, ManagerContainerActionReference::values(), 'system.exceptions.SY0017.container_actions_is_not_available');

        $this->value = $value;
    }

    public function toArray(): array
    {
        return $this->value;
    }
}
