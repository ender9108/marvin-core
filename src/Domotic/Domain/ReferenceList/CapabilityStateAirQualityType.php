<?php

namespace App\Domotic\Domain\ReferenceList;

use EnderLab\ToolsBundle\Service\EnumToArrayTrait;

enum CapabilityStateAirQualityType: string
{
    use EnumToArrayTrait;

    case EXCELLENT = 'excellent';
    case GOOD = 'good';
    case MODERATE = 'moderate';
    case POOR = 'poor';
    case UNHEALTHY = 'unhealthy';
    case HAZARDOUS = 'hazardous';
}
