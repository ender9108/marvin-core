<?php

namespace Marvin\Device\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectInterface;
use EnderLab\ToolsBundle\Service\EnumToArrayTrait;

enum CapabilityCategory: string implements ValueObjectInterface
{
    use EnumToArrayTrait;

    case LIGHTING = 'lighting';
    case ENERGY = 'energy';
    case ENVIRONMENTAL = 'environmental';
    case SECURITY = 'security';
    case CLIMATE = 'climate';
    case COVERING = 'covering';
    case AUDIO_VIDEO = 'audio_video';
    case CONTROL = 'control';
    case MEASUREMENT = 'measurement';
    case COMMUNICATION = 'communication';
    case SYSTEM = 'system';
    case COMPOSITE = 'composite';

    public function equals(ValueObjectInterface $other): bool
    {
        return $other instanceof self && $this->value === $other->value;
    }

    public function toString(): string
    {
        return $this->value;
    }
}
