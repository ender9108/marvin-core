<?php

namespace Marvin\Device\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectInterface;
use EnderLab\ToolsBundle\Service\EnumToArrayTrait;

/**
 * Capability - Capacités disponibles pour les devices
 *
 * Représente toutes les capabilities qu'un device peut supporter.
 * Une capability définit ce qu'un device peut faire ou mesurer.
 *
 * Total : 145 capabilities
 */
enum Capability: string implements ValueObjectInterface
{
    use EnumToArrayTrait;

    // ==========================================
    // ÉCLAIRAGE (7)
    // ==========================================

    case SWITCH = 'switch';
    case LIGHT = 'light';
    case BRIGHTNESS = 'brightness';
    case COLOR_TEMPERATURE = 'color_temperature';
    case COLOR_CONTROL = 'color_control';
    case COLOR_MODE = 'color_mode';
    case LIGHT_EFFECT = 'light_effect';

    // ==========================================
    // ÉNERGIE (6)
    // ==========================================

    case POWER_METER = 'power_meter';
    case ENERGY_METER = 'energy_meter';
    case VOLTAGE_MEASUREMENT = 'voltage_measurement';
    case CURRENT_MEASUREMENT = 'current_measurement';
    case POWER_FACTOR = 'power_factor';
    case POWER_SOURCE = 'power_source';

    // ==========================================
    // CAPTEURS ENVIRONNEMENTAUX (11)
    // ==========================================

    case TEMPERATURE_MEASUREMENT = 'temperature_measurement';
    case HUMIDITY_MEASUREMENT = 'humidity_measurement';
    case PRESSURE_MEASUREMENT = 'pressure_measurement';
    case AIR_QUALITY = 'air_quality';
    case CARBON_DIOXIDE_MEASUREMENT = 'carbon_dioxide_measurement';
    case CARBON_MONOXIDE_DETECTOR = 'carbon_monoxide_detector';
    case VOC_MEASUREMENT = 'voc_measurement';
    case PM25_MEASUREMENT = 'pm25_measurement';
    case ILLUMINANCE_MEASUREMENT = 'illuminance_measurement';
    case UV_INDEX = 'uv_index';
    case NOISE_LEVEL = 'noise_level';

    // ==========================================
    // SÉCURITÉ (13)
    // ==========================================

    case MOTION_SENSOR = 'motion_sensor';
    case OCCUPANCY_SENSOR = 'occupancy_sensor';
    case CONTACT_SENSOR = 'contact_sensor';
    case VIBRATION_SENSOR = 'vibration_sensor';
    case WATER_LEAK_DETECTOR = 'water_leak_detector';
    case SMOKE_DETECTOR = 'smoke_detector';
    case GLASS_BREAK_DETECTOR = 'glass_break_detector';
    case TAMPER_ALERT = 'tamper_alert';
    case LOCK = 'lock';
    case DOOR_CONTROL = 'door_control';
    case ALARM = 'alarm';
    case SECURITY_SYSTEM = 'security_system';
    case CAMERA = 'camera';

    // ==========================================
    // CLIMAT (15) - Ajout AIR_PURIFIER_MODE, HUMIDITY_CONTROL
    // ==========================================

    case THERMOSTAT_MODE = 'thermostat_mode';
    case THERMOSTAT_HEATING_SETPOINT = 'thermostat_heating_setpoint';
    case THERMOSTAT_COOLING_SETPOINT = 'thermostat_cooling_setpoint';
    case THERMOSTAT_FAN_MODE = 'thermostat_fan_mode';
    case THERMOSTAT_OPERATING_STATE = 'thermostat_operating_state';
    case FAN_SPEED = 'fan_speed';
    case FAN_MODE = 'fan_mode';
    case FAN_OSCILLATION = 'fan_oscillation';
    case HEATER_COOLER = 'heater_cooler';
    case HUMIDIFIER = 'humidifier';
    case DEHUMIDIFIER = 'dehumidifier';
    case HUMIDITY_CONTROL = 'humidity_control'; // NOUVEAU - Contrôle humidité cible
    case AIR_PURIFIER = 'air_purifier';
    case AIR_PURIFIER_MODE = 'air_purifier_mode'; // NOUVEAU - Mode purificateur
    case FILTER_STATUS = 'filter_status';

    // ==========================================
    // COUVERTURES / VOLETS (4)
    // ==========================================

    case WINDOW_COVERING = 'window_covering';
    case WINDOW_COVERING_POSITION = 'window_covering_position';
    case WINDOW_COVERING_TILT = 'window_covering_tilt';
    case VALVE = 'valve';

    // ==========================================
    // AUDIO / VIDÉO (12) - Ajout MEDIA_REPEAT, MEDIA_SHUFFLE, MEDIA_SEEK
    // ==========================================

    case AUDIO_VOLUME = 'audio_volume';
    case AUDIO_MUTE = 'audio_mute';
    case MEDIA_PLAYBACK = 'media_playback';
    case MEDIA_TRACK_CONTROL = 'media_track_control';
    case MEDIA_REPEAT = 'media_repeat'; // NOUVEAU - Mode répétition
    case MEDIA_SHUFFLE = 'media_shuffle'; // NOUVEAU - Mode aléatoire
    case MEDIA_SEEK = 'media_seek'; // NOUVEAU - Chercher position
    case INPUT_SOURCE = 'input_source';
    case TV_CHANNEL = 'tv_channel';
    case SPEAKER = 'speaker';
    case MEDIA_CONTENT = 'media_content'; // NOUVEAU - Contenu média (titre, artiste, etc.)
    case MEDIA_IMAGE = 'media_image'; // NOUVEAU - Image du média (album art)

    // ==========================================
    // CONTRÔLE (6)
    // ==========================================

    case BUTTON = 'button';
    case MULTI_BUTTON = 'multi_button';
    case ROTARY_ENCODER = 'rotary_encoder';
    case MOMENTARY = 'momentary';
    case INDICATOR = 'indicator';
    case NOTIFICATION = 'notification';

    // ==========================================
    // MESURE (6)
    // ==========================================

    case BATTERY = 'battery';
    case BATTERY_VOLTAGE = 'battery_voltage';
    case SIGNAL_STRENGTH = 'signal_strength';
    case LINK_QUALITY = 'link_quality';
    case NETWORK_STATUS = 'network_status';
    case REACHABLE = 'reachable';

    // ==========================================
    // COMMUNICATION (7)
    // ==========================================

    case PRESENCE_SENSOR = 'presence_sensor';
    case PROXIMITY_SENSOR = 'proximity_sensor';
    case BEACON = 'beacon';
    case IR_TRANSMITTER = 'ir_transmitter';
    case IR_RECEIVER = 'ir_receiver';
    case RF_TRANSMITTER = 'rf_transmitter';
    case RF_RECEIVER = 'rf_receiver';

    // ==========================================
    // SYSTÈME (7)
    // ==========================================

    case CONFIGURATION = 'configuration';
    case UPDATE = 'update';
    case IDENTIFY = 'identify';
    case HEALTH_CHECK = 'health_check';
    case DIAGNOSTICS = 'diagnostics';
    case TIME_SYNC = 'time_sync';
    case LOCATION = 'location';

    // ==========================================
    // NETTOYAGE (3) - NOUVEAU
    // ==========================================

    case VACUUM_CONTROL = 'vacuum_control';
    case VACUUM_FAN_SPEED = 'vacuum_fan_speed';
    case VACUUM_ZONE = 'vacuum_zone'; // Nettoyage de zones spécifiques

    // ==========================================
    // JARDIN / IRRIGATION (3) - NOUVEAU
    // ==========================================

    case SPRINKLER = 'sprinkler';
    case IRRIGATION_CONTROL = 'irrigation_control';
    case SOIL_MOISTURE = 'soil_moisture';

    // ==========================================
    // CAPABILITIES COMPOSITES (11)
    // ==========================================

    case THERMOSTAT = 'thermostat';
    case RGBW_LIGHT = 'rgbw_light';
    case ENVIRONMENTAL_SENSOR = 'environmental_sensor';
    case SECURITY_SENSOR = 'security_sensor';
    case SMART_PLUG = 'smart_plug';
    case SMART_LOCK = 'smart_lock';
    case WEATHER_STATION = 'weather_station';
    case AIR_QUALITY_SENSOR = 'air_quality_sensor';
    case SMART_FAN = 'smart_fan';
    case MOTORIZED_BLIND = 'motorized_blind';
    case SMART_THERMOSTAT_VALVE = 'smart_thermostat_valve';

    // ==========================================
    // CAPABILITIES VIRTUELLES (13) - Ajout WEBHOOK, HTTP_REQUEST, etc.
    // ==========================================

    case CURRENT_TIME = 'current_time';
    case CURRENT_DATE = 'current_date';
    case CURRENT_DAY_OF_WEEK = 'current_day_of_week';
    case SUNRISE_TIME = 'sunrise_time';
    case SUNSET_TIME = 'sunset_time';
    case IS_DAY = 'is_day';
    case WEBHOOK = 'webhook'; // NOUVEAU - Déclencher webhook
    case HTTP_REQUEST = 'http_request'; // NOUVEAU - Requête HTTP
    case SCRIPT_EXECUTION = 'script_execution'; // NOUVEAU - Exécuter script
    case SCENE_CONTROL = 'scene_control'; // NOUVEAU - Contrôle scènes
    case AUTOMATION_TRIGGER = 'automation_trigger'; // NOUVEAU - Déclencher automation
    case COUNTER = 'counter'; // NOUVEAU - Compteur virtuel
    case TIMER = 'timer'; // NOUVEAU - Timer/minuteur

    // ==========================================
    // MÉTHODES
    // ==========================================

    public function toString(): string
    {
        return $this->value;
    }

    public function equals(ValueObjectInterface $other): bool
    {
        return $other instanceof self && $this->value === $other->value;
    }

    public function getCategory(): CapabilityCategory
    {
        return match ($this) {
            self::SWITCH, self::LIGHT, self::BRIGHTNESS, self::COLOR_TEMPERATURE,
            self::COLOR_CONTROL, self::COLOR_MODE, self::LIGHT_EFFECT
            => CapabilityCategory::LIGHTING,

            self::POWER_METER, self::ENERGY_METER, self::VOLTAGE_MEASUREMENT,
            self::CURRENT_MEASUREMENT, self::POWER_FACTOR, self::POWER_SOURCE
            => CapabilityCategory::ENERGY,

            self::TEMPERATURE_MEASUREMENT, self::HUMIDITY_MEASUREMENT, self::PRESSURE_MEASUREMENT,
            self::AIR_QUALITY, self::CARBON_DIOXIDE_MEASUREMENT, self::CARBON_MONOXIDE_DETECTOR,
            self::VOC_MEASUREMENT, self::PM25_MEASUREMENT, self::ILLUMINANCE_MEASUREMENT,
            self::UV_INDEX, self::NOISE_LEVEL
            => CapabilityCategory::ENVIRONMENTAL,

            self::MOTION_SENSOR, self::OCCUPANCY_SENSOR, self::CONTACT_SENSOR,
            self::VIBRATION_SENSOR, self::WATER_LEAK_DETECTOR, self::SMOKE_DETECTOR,
            self::GLASS_BREAK_DETECTOR, self::TAMPER_ALERT, self::LOCK,
            self::DOOR_CONTROL, self::ALARM, self::SECURITY_SYSTEM, self::CAMERA
            => CapabilityCategory::SECURITY,

            self::THERMOSTAT_MODE, self::THERMOSTAT_HEATING_SETPOINT, self::THERMOSTAT_COOLING_SETPOINT,
            self::THERMOSTAT_FAN_MODE, self::THERMOSTAT_OPERATING_STATE, self::FAN_SPEED,
            self::FAN_MODE, self::FAN_OSCILLATION, self::HEATER_COOLER,
            self::HUMIDIFIER, self::DEHUMIDIFIER, self::AIR_PURIFIER, self::FILTER_STATUS
            => CapabilityCategory::CLIMATE,

            self::WINDOW_COVERING, self::WINDOW_COVERING_POSITION, self::WINDOW_COVERING_TILT,
            self::VALVE
            => CapabilityCategory::COVERING,

            self::AUDIO_VOLUME, self::AUDIO_MUTE, self::MEDIA_PLAYBACK,
            self::MEDIA_TRACK_CONTROL, self::INPUT_SOURCE, self::TV_CHANNEL,
            self::SPEAKER
            => CapabilityCategory::AUDIO_VIDEO,

            self::BUTTON, self::MULTI_BUTTON, self::ROTARY_ENCODER,
            self::MOMENTARY, self::INDICATOR, self::NOTIFICATION
            => CapabilityCategory::CONTROL,

            self::BATTERY, self::BATTERY_VOLTAGE, self::SIGNAL_STRENGTH,
            self::LINK_QUALITY, self::NETWORK_STATUS, self::REACHABLE
            => CapabilityCategory::MEASUREMENT,

            self::PRESENCE_SENSOR, self::PROXIMITY_SENSOR, self::BEACON,
            self::IR_TRANSMITTER, self::IR_RECEIVER, self::RF_TRANSMITTER,
            self::RF_RECEIVER
            => CapabilityCategory::COMMUNICATION,

            self::CONFIGURATION, self::UPDATE, self::IDENTIFY,
            self::HEALTH_CHECK, self::DIAGNOSTICS, self::TIME_SYNC,
            self::LOCATION
            => CapabilityCategory::SYSTEM,

            self::THERMOSTAT, self::RGBW_LIGHT, self::ENVIRONMENTAL_SENSOR,
            self::SECURITY_SENSOR, self::SMART_PLUG, self::SMART_LOCK,
            self::WEATHER_STATION, self::AIR_QUALITY_SENSOR, self::SMART_FAN,
            self::MOTORIZED_BLIND, self::SMART_THERMOSTAT_VALVE
            => CapabilityCategory::COMPOSITE,
        };
    }

    public function isReadOnly(): bool
    {
        return match ($this) {
            // Capteurs (lecture seule)
            self::TEMPERATURE_MEASUREMENT,
            self::HUMIDITY_MEASUREMENT,
            self::PRESSURE_MEASUREMENT,
            self::AIR_QUALITY,
            self::CARBON_DIOXIDE_MEASUREMENT,
            self::CARBON_MONOXIDE_DETECTOR,
            self::VOC_MEASUREMENT,
            self::PM25_MEASUREMENT,
            self::ILLUMINANCE_MEASUREMENT,
            self::UV_INDEX,
            self::NOISE_LEVEL,
            self::MOTION_SENSOR,
            self::OCCUPANCY_SENSOR,
            self::CONTACT_SENSOR,
            self::VIBRATION_SENSOR,
            self::WATER_LEAK_DETECTOR,
            self::SMOKE_DETECTOR,
            self::GLASS_BREAK_DETECTOR,
            self::TAMPER_ALERT,
            self::BATTERY,
            self::BATTERY_VOLTAGE,
            self::SIGNAL_STRENGTH,
            self::LINK_QUALITY,
            self::NETWORK_STATUS,
            self::REACHABLE,
            self::PRESENCE_SENSOR,
            self::PROXIMITY_SENSOR,
            self::POWER_METER,
            self::ENERGY_METER,
            self::VOLTAGE_MEASUREMENT,
            self::CURRENT_MEASUREMENT,
            self::POWER_FACTOR,
            self::THERMOSTAT_OPERATING_STATE,
            self::FILTER_STATUS,
            self::SOIL_MOISTURE,
            self::CURRENT_TIME,
            self::CURRENT_DATE,
            self::CURRENT_DAY_OF_WEEK,
            self::SUNRISE_TIME,
            self::SUNSET_TIME,
            self::IS_DAY,
            self::MEDIA_CONTENT,
            self::MEDIA_IMAGE
            => true,

            // Tous les autres sont contrôlables
            default => false,
        };
    }

    /**
     * Indique si la capability est virtuelle
     */
    public function isVirtual(): bool
    {
        return match ($this) {
            self::CURRENT_TIME,
            self::CURRENT_DATE,
            self::CURRENT_DAY_OF_WEEK,
            self::SUNRISE_TIME,
            self::SUNSET_TIME,
            self::IS_DAY,
            self::WEBHOOK,
            self::HTTP_REQUEST,
            self::SCRIPT_EXECUTION,
            self::SCENE_CONTROL,
            self::AUTOMATION_TRIGGER,
            self::COUNTER,
            self::TIMER
            => true,

            default => false,
        };
    }

    /**
     * Indique si la capability est composite (regroupe plusieurs capabilities)
     */
    public function isComposite(): bool
    {
        return match ($this) {
            self::THERMOSTAT,
            self::RGBW_LIGHT,
            self::ENVIRONMENTAL_SENSOR,
            self::SECURITY_SENSOR,
            self::SMART_PLUG,
            self::SMART_LOCK,
            self::WEATHER_STATION,
            self::AIR_QUALITY_SENSOR,
            self::SMART_FAN,
            self::MOTORIZED_BLIND,
            self::SMART_THERMOSTAT_VALVE
            => true,

            default => false,
        };
    }

    /**
     * Retourne les capabilities de base pour une capability composite
     *
     * @return self[]
     */
    public function getBaseCapabilities(): array
    {
        if (!$this->isComposite()) {
            return [$this];
        }

        return match ($this) {
            self::THERMOSTAT => [
                self::THERMOSTAT_MODE,
                self::THERMOSTAT_HEATING_SETPOINT,
                self::THERMOSTAT_COOLING_SETPOINT,
                self::THERMOSTAT_FAN_MODE,
                self::THERMOSTAT_OPERATING_STATE,
                self::TEMPERATURE_MEASUREMENT,
            ],

            self::RGBW_LIGHT => [
                self::LIGHT,
                self::BRIGHTNESS,
                self::COLOR_CONTROL,
                self::COLOR_TEMPERATURE,
                self::LIGHT_EFFECT,
            ],

            self::ENVIRONMENTAL_SENSOR => [
                self::TEMPERATURE_MEASUREMENT,
                self::HUMIDITY_MEASUREMENT,
                self::PRESSURE_MEASUREMENT,
            ],

            self::SECURITY_SENSOR => [
                self::MOTION_SENSOR,
                self::CONTACT_SENSOR,
                self::TAMPER_ALERT,
            ],

            self::SMART_PLUG => [
                self::SWITCH,
                self::POWER_METER,
                self::ENERGY_METER,
            ],

            self::SMART_LOCK => [
                self::LOCK,
                self::BATTERY,
                self::TAMPER_ALERT,
            ],

            self::WEATHER_STATION => [
                self::TEMPERATURE_MEASUREMENT,
                self::HUMIDITY_MEASUREMENT,
                self::PRESSURE_MEASUREMENT,
                self::ILLUMINANCE_MEASUREMENT,
                self::UV_INDEX,
            ],

            self::AIR_QUALITY_SENSOR => [
                self::AIR_QUALITY,
                self::CARBON_DIOXIDE_MEASUREMENT,
                self::VOC_MEASUREMENT,
                self::PM25_MEASUREMENT,
            ],

            self::SMART_FAN => [
                self::SWITCH,
                self::FAN_SPEED,
                self::FAN_OSCILLATION,
            ],

            self::MOTORIZED_BLIND => [
                self::WINDOW_COVERING,
                self::WINDOW_COVERING_POSITION,
                self::WINDOW_COVERING_TILT,
            ],

            self::SMART_THERMOSTAT_VALVE => [
                self::THERMOSTAT_HEATING_SETPOINT,
                self::TEMPERATURE_MEASUREMENT,
                self::BATTERY,
            ],

            default => [$this],
        };
    }
}
