<?php

namespace Marvin\Device\Domain\ValueObject;

use EnderLab\ToolsBundle\Service\EnumToArrayTrait;

enum CapabilityCategory: string
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
}
