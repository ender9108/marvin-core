<?php

namespace Marvin\System\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\MarvinManagerBundle\Reference\ManagerContainerActionReference;

final readonly class ContainerAllowedActions
{
    public array $value;

    public function __construct(array $value = [])
    {
        Assert::notEmpty($value);
        Assert::allInArray($value, ManagerContainerActionReference::values());

        $this->value = $value;
    }

    public function toArray(): array
    {
        return $this->value;
    }
}
