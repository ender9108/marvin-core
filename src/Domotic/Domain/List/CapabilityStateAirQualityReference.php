<?php

namespace Marvin\Domotic\Domain\List;

use EnderLab\ToolsBundle\Service\EnumToArrayTrait;

enum CapabilityStateAirQualityReference: string
{
    use EnumToArrayTrait;

    case EXCELLENT = 'excellent';
    case GOOD = 'good';
    case MODERATE = 'moderate';
    case POOR = 'poor';
    case UNHEALTHY = 'unhealthy';
    case HAZARDOUS = 'hazardous';
}
