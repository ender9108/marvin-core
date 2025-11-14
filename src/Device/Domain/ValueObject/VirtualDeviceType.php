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
 * VirtualDeviceType - Type de device virtuel
 *
 * Détermine la source de données et le comportement d'un device virtuel
 */
enum VirtualDeviceType: string
{
    use EnumToArrayTrait;
    use ValueObjectTrait;

    // ========== Time-based Devices ==========

    /**
     * TIME_TRIGGER - Déclencheur basé sur l'heure
     * Déclenche des événements à des horaires spécifiques
     * Config: times (array of HH:MM), days (optional)
     */
    case TIME_TRIGGER = 'time_trigger';

    /**
     * SUN_TRIGGER - Déclencheur basé sur le lever/coucher du soleil
     * Calcule automatiquement sunrise, sunset, dawn, dusk
     * Config: latitude, longitude, offset (optional)
     */
    case SUN_TRIGGER = 'sun_trigger';

    /**
     * TIMER - Minuteur avec durée configurable
     * Déclenche après un délai spécifique
     * Config: duration (seconds), auto_reset (bool)
     */
    case TIMER = 'timer';

    /**
     * COUNTER - Compteur d'événements
     * Compte le nombre d'occurrences d'un événement
     * Config: initial_value, max_value (optional)
     */
    case COUNTER = 'counter';

    // ========== Weather Devices ==========

    /**
     * WEATHER - Données météo en temps réel
     * Récupère température, humidité, pression, etc.
     * Config: provider (openweathermap, etc.), api_key, location
     */
    case WEATHER = 'weather';

    /**
     * WEATHER_ALERT - Alertes météo
     * Notifie en cas d'événements météo importants
     * Config: provider, api_key, location, alert_types
     */
    case WEATHER_ALERT = 'weather_alert';

    // ========== Network & System Devices ==========

    /**
     * HTTP_TRIGGER - Déclencheur HTTP/Webhook
     * Reçoit des données via HTTP POST/GET
     * Config: endpoint, authentication (optional)
     */
    case HTTP_TRIGGER = 'http_trigger';

    /**
     * MQTT_VIRTUAL - Device virtuel MQTT
     * Écoute un topic MQTT et expose les valeurs
     * Config: topic, qos (optional)
     */
    case MQTT_VIRTUAL = 'mqtt_virtual';

    /**
     * PRESENCE_VIRTUAL - Détection de présence virtuelle
     * Agrège plusieurs sensors de présence
     * Config: device_ids (array), timeout (seconds)
     */
    case PRESENCE_VIRTUAL = 'presence_virtual';

    /**
     * DEVICE_TRACKER - Suivi de device (smartphone, etc.)
     * Détecte la présence via ping, bluetooth, etc.
     * Config: method (ping, bluetooth), address, interval
     */
    case DEVICE_TRACKER = 'device_tracker';

    // ========== Variables & Storage ==========

    /**
     * VARIABLE - Variable stockée en mémoire
     * Stocke une valeur modifiable par automations
     * Config: initial_value, type (string, int, float, bool)
     */
    case VARIABLE = 'variable';

    /**
     * STORAGE - Stockage persistant
     * Stocke des données de manière persistante
     * Config: initial_value, type
     */
    case STORAGE = 'storage';

    // ========== Logic Devices ==========

    /**
     * CONDITION - Condition logique
     * Évalue une condition et retourne true/false
     * Config: condition (expression), device_ids (optional)
     */
    case CONDITION = 'condition';

    /**
     * SCENE - Scène (ensemble d'états)
     * DEPRECATED: Utiliser DeviceType::COMPOSITE avec CompositeType::SCENE
     */
    case SCENE = 'scene';

    /**
     * SCRIPT - Script exécutable
     * Séquence d'actions prédéfinies
     * Config: actions (array), delay_between_actions (optional)
     */
    case SCRIPT = 'script';

    // ========== Notifications ==========

    /**
     * NOTIFIER - Notificateur
     * Envoie des notifications (email, push, SMS)
     * Config: provider (email, push, sms), credentials
     */
    case NOTIFIER = 'notifier';

    /**
     * TTS - Text-to-Speech
     * Convertit du texte en parole
     * Config: provider (google, amazon), language, voice
     */
    case TTS = 'tts';

    // ========== External Integrations ==========

    /**
     * CALENDAR - Intégration calendrier
     * Récupère des événements depuis un calendrier externe
     * Config: provider (google, caldav), credentials, calendar_id
     */
    case CALENDAR = 'calendar';

    /**
     * RSS_FEED - Flux RSS
     * Récupère et parse des flux RSS
     * Config: url, update_interval (seconds)
     */
    case RSS_FEED = 'rss_feed';
}
