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

/**
 * DeviceType - Type de device
 *
 * Détermine le type fondamental du device dans le système
 */
enum DeviceType: string
{
    use EnumToArrayTrait;
    use ValueObjectTrait;

    /**
     * ACTUATOR - Device physique pouvant exécuter des actions
     * Exemples : Ampoule, interrupteur, thermostat, volet
     */
    case ACTUATOR = 'actuator';

    /**
     * SENSOR - Device physique en lecture seule
     * Exemples : Capteur de température, détecteur de mouvement, capteur de porte
     */
    case SENSOR = 'sensor';

    /**
     * COMPOSITE - Device composite (groupe ou scène)
     * Regroupe plusieurs devices pour contrôle unifié
     */
    case COMPOSITE = 'composite';

    /**
     * VIRTUAL - Device virtuel basé sur des données externes
     * Exemples : Météo, lever/coucher du soleil, API REST externe
     */
    case VIRTUAL = 'virtual';
}
