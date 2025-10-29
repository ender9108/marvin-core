<?php

namespace Marvin\Device\Domain\ValueObject;

enum CapabilityStateDataType: string implements Stringable
{
    case BOOLEAN = 'boolean';
    case INTEGER = 'integer';
    case FLOAT = 'float';
    case STRING = 'string';
    case DATETIME = 'datetime';
    case OBJECT = 'object'; // JSON object (ex: {r, g, b})
    case ARRAY = 'array'; // JSON array
}
