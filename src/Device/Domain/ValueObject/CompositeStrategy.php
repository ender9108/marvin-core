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

    /**
     * Broadcast classique (envoie à tous les devices simultanément)
     * Pour groupes simples
     */
    case BROADCAST = 'broadcast';

    /**
     * Séquentiel (un par un avec délai)
     * Pour scénarios complexes nécessitant un ordre
     */
    case SEQUENTIAL = 'sequential';

    /**
     * Agrégation des réponses
     * Pour capteurs multiples (moyenne, somme, etc.)
     */
    case AGGREGATE = 'aggregate';

    /**
     * Premier qui répond
     * Pour queries où on veut juste une réponse rapide
     */
    case FIRST_RESPONSE = 'first_response';
}
