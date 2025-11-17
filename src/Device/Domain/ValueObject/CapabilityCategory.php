<?php

/**
 * Marvin Core - DDD-based home automation system
 *
 * @package   Marvin\Core
 * @author    Alexandre Berthelot <alexandreberthelot9108@gmail.com>
 * @copyright 2024-present Alexandre Berthelot
 * @license   AGPL-3.0 License
 * @link      https://github.com/ender9108/marvin-core
 */

declare(strict_types=1);

namespace Marvin\Device\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectTrait;
use EnderLab\ToolsBundle\Service\EnumToArrayTrait;

enum CapabilityCategory: string
{
    use EnumToArrayTrait;
    use ValueObjectTrait;

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
