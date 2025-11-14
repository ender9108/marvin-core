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

enum CompositeStrategy: string
{
    /**
     * Utilise le groupe/scène natif du protocole SI DISPONIBLE
     * Sinon fallback sur émulation Marvin
     * → Stratégie recommandée par défaut
     */
    case NATIVE_IF_AVAILABLE = 'native_if_available';

    /**
     * Force l'utilisation du natif uniquement
     * Erreur si le protocole ne supporte pas les groupes/scènes natifs
     */
    case NATIVE_ONLY = 'native_only';

    /**
     * Force l'émulation Marvin
     * Ignore le natif même si disponible
     */
    case EMULATED_ONLY = 'emulated_only';

    public function toString(): string
    {
        return $this->value;
    }
}
