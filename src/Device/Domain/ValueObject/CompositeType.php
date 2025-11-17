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
 * CompositeType - Type de device composite
 *
 * Définit le type de composition de plusieurs devices
 */
enum CompositeType: string
{
    use EnumToArrayTrait;
    use ValueObjectTrait;

    /**
     * GROUP - Groupe de devices du même type
     * Envoie la même commande à tous les devices membres
     * Peut être natif (protocole) ou émulé
     */
    case GROUP = 'group';

    /**
     * SCENE - Scène avec états prédéfinis
     * Restaure des états spécifiques pour chaque device
     * Peut être native (protocole) ou émulée
     */
    case SCENE = 'scene';
}
