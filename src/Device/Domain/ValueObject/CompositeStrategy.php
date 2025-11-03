<?php

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
