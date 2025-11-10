<?php

namespace Marvin\Device\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectTrait;
use EnderLab\ToolsBundle\Service\EnumToArrayTrait;

enum ExecutionStrategy: string
{
    use EnumToArrayTrait;
    use ValueObjectTrait;

    case BROADCAST = 'broadcast';
    case SEQUENTIAL = 'sequential';
    case FIRST_RESPONSE = 'first_response';
    case AGGREGATE = 'aggregate';

    public function toString(): string
    {
        return $this->value;
    }
}
