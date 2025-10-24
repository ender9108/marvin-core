<?php

namespace Marvin\Device\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectInterface;

enum CapabilityStateDataType: string implements ValueObjectInterface
{
    case BOOLEAN = 'boolean';
    case INTEGER = 'integer';
    case FLOAT = 'float';
    case STRING = 'string';
    case DATETIME = 'datetime';
    case OBJECT = 'object'; // JSON object (ex: {r, g, b})
    case ARRAY = 'array'; // JSON array
}
