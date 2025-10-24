<?php

namespace Marvin\Device\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectInterface;

/**
 * CapabilityAction
 *
 * Représente toutes les actions qui peuvent être exécutées sur un device.
 * Chaque action est liée à une ou plusieurs capabilities.
 *
 * Total : 142 actions
 */
enum CapabilityAction: string implements ValueObjectInterface
{
    // ==========================================
    // ÉCLAIRAGE & SWITCH
    // ==========================================

    // Switch / Light basique
    case TURN_ON = 'turn_on';
    case TURN_OFF = 'turn_off';
    case TOGGLE = 'toggle';

    // Brightness
    case SET_BRIGHTNESS = 'set_brightness';
    case INCREASE_BRIGHTNESS = 'increase_brightness';
    case DECREASE_BRIGHTNESS = 'decrease_brightness';

    // Color Temperature
    case SET_COLOR_TEMPERATURE = 'set_color_temperature';
    case SET_COLOR_TEMPERATURE_MIRED = 'set_color_temperature_mired';
    case SET_WARM_WHITE = 'set_warm_white';
    case SET_COOL_WHITE = 'set_cool_white';
    case SET_NEUTRAL_WHITE = 'set_neutral_white';

    // Color Control
    case SET_COLOR = 'set_color';
    case SET_COLOR_RGB = 'set_color_rgb';
    case SET_COLOR_HSV = 'set_color_hsv';
    case SET_COLOR_XY = 'set_color_xy';
    case SET_COLOR_HEX = 'set_color_hex';
    case SET_HUE = 'set_hue';
    case SET_SATURATION = 'set_saturation';

    // Light Effect
    case SET_EFFECT = 'set_effect';
    case START_COLOR_LOOP = 'start_color_loop';
    case STOP_EFFECT = 'stop_effect';
    case BLINK = 'blink';
    case BREATHE = 'breathe';

    // ==========================================
    // ÉNERGIE
    // ==========================================

    case RESET_ENERGY = 'reset_energy';

    // ==========================================
    // SÉCURITÉ
    // ==========================================

    // Lock
    case LOCK = 'lock';
    case UNLOCK = 'unlock';
    case UNLOCK_WITH_TIMEOUT = 'unlock_with_timeout';

    // Door Control
    case OPEN = 'open';
    case CLOSE = 'close';
    case STOP = 'stop';

    // Alarm
    case ARM = 'arm';
    case ARM_AWAY = 'arm_away';
    case ARM_HOME = 'arm_home';
    case ARM_NIGHT = 'arm_night';
    case DISARM = 'disarm';
    case TRIGGER = 'trigger';

    // Camera
    case START_STREAMING = 'start_streaming';
    case STOP_STREAMING = 'stop_streaming';
    case START_RECORDING = 'start_recording';
    case STOP_RECORDING = 'stop_recording';
    case TAKE_PICTURE = 'take_picture';
    case PAN_LEFT = 'pan_left';
    case PAN_RIGHT = 'pan_right';
    case TILT_UP = 'tilt_up';
    case TILT_DOWN = 'tilt_down';
    case ZOOM_IN = 'zoom_in';
    case ZOOM_OUT = 'zoom_out';
    case GO_TO_PRESET = 'go_to_preset';

    // ==========================================
    // CLIMAT
    // ==========================================

    // Thermostat Mode
    case SET_THERMOSTAT_MODE = 'set_thermostat_mode';
    case SET_MODE_OFF = 'set_mode_off';
    case SET_MODE_HEAT = 'set_mode_heat';
    case SET_MODE_COOL = 'set_mode_cool';
    case SET_MODE_AUTO = 'set_mode_auto';

    // Thermostat Setpoint
    case SET_HEATING_SETPOINT = 'set_heating_setpoint';
    case INCREASE_HEATING_SETPOINT = 'increase_heating_setpoint';
    case DECREASE_HEATING_SETPOINT = 'decrease_heating_setpoint';
    case SET_COOLING_SETPOINT = 'set_cooling_setpoint';
    case INCREASE_COOLING_SETPOINT = 'increase_cooling_setpoint';
    case DECREASE_COOLING_SETPOINT = 'decrease_cooling_setpoint';

    // Fan
    case SET_FAN_MODE = 'set_fan_mode';
    case SET_FAN_AUTO = 'set_fan_auto';
    case SET_FAN_ON = 'set_fan_on';
    case SET_FAN_SPEED = 'set_fan_speed';
    case SET_FAN_SPEED_PERCENT = 'set_fan_speed_percent';
    case INCREASE_FAN_SPEED = 'increase_fan_speed';
    case DECREASE_FAN_SPEED = 'decrease_fan_speed';
    case SET_OSCILLATION = 'set_oscillation';
    case ENABLE_OSCILLATION = 'enable_oscillation';
    case DISABLE_OSCILLATION = 'disable_oscillation';

    // Humidifier / Dehumidifier - NOUVEAU
    case SET_HUMIDITY_TARGET = 'set_humidity_target';
    case INCREASE_HUMIDITY_TARGET = 'increase_humidity_target';
    case DECREASE_HUMIDITY_TARGET = 'decrease_humidity_target';

    // Air Purifier - NOUVEAU
    case SET_PURIFIER_MODE = 'set_purifier_mode';
    case SET_PURIFIER_SPEED = 'set_purifier_speed';
    case INCREASE_PURIFIER_SPEED = 'increase_purifier_speed';
    case DECREASE_PURIFIER_SPEED = 'decrease_purifier_speed';

    // ==========================================
    // COUVERTURES / VOLETS
    // ==========================================

    // Position & Tilt
    case SET_POSITION = 'set_position';
    case OPEN_TO_POSITION = 'open_to_position';
    case CLOSE_TO_POSITION = 'close_to_position';
    case SET_TILT = 'set_tilt';
    case OPEN_TILT = 'open_tilt';
    case CLOSE_TILT = 'close_tilt';

    // Valve
    case OPEN_VALVE = 'open_valve';
    case CLOSE_VALVE = 'close_valve';
    case SET_VALVE_POSITION = 'set_valve_position';

    // ==========================================
    // AUDIO / VIDÉO
    // ==========================================

    // Volume
    case SET_VOLUME = 'set_volume';
    case INCREASE_VOLUME = 'increase_volume';
    case DECREASE_VOLUME = 'decrease_volume';
    case VOLUME_UP = 'volume_up';
    case VOLUME_DOWN = 'volume_down';

    // Mute
    case MUTE = 'mute';
    case UNMUTE = 'unmute';
    case TOGGLE_MUTE = 'toggle_mute';

    // Playback
    case PLAY = 'play';
    case PAUSE = 'pause';
    case PLAY_PAUSE = 'play_pause';
    case STOP_MEDIA = 'stop_media'; // NOUVEAU - distinct de STOP (volets)

    // Track Control
    case NEXT_TRACK = 'next_track';
    case PREVIOUS_TRACK = 'previous_track';
    case SKIP_FORWARD = 'skip_forward';
    case SKIP_BACKWARD = 'skip_backward';

    // Repeat & Shuffle - NOUVEAU
    case SET_REPEAT_MODE = 'set_repeat_mode';
    case ENABLE_REPEAT = 'enable_repeat';
    case DISABLE_REPEAT = 'disable_repeat';
    case SET_SHUFFLE = 'set_shuffle';
    case ENABLE_SHUFFLE = 'enable_shuffle';
    case DISABLE_SHUFFLE = 'disable_shuffle';

    // Seek - NOUVEAU
    case SEEK_TO_POSITION = 'seek_to_position';
    case SEEK_FORWARD = 'seek_forward';
    case SEEK_BACKWARD = 'seek_backward';

    // Input Source
    case SET_INPUT_SOURCE = 'set_input_source';
    case NEXT_INPUT = 'next_input';
    case PREVIOUS_INPUT = 'previous_input';

    // TV Channel
    case SET_CHANNEL = 'set_channel';
    case CHANNEL_UP = 'channel_up';
    case CHANNEL_DOWN = 'channel_down';

    // ==========================================
    // VACUUM (ASPIRATEUR) - NOUVEAU
    // ==========================================

    case START_CLEANING = 'start_cleaning';
    case PAUSE_CLEANING = 'pause_cleaning';
    case STOP_CLEANING = 'stop_cleaning';
    case RETURN_TO_BASE = 'return_to_base';
    case LOCATE_VACUUM = 'locate_vacuum';
    case CLEAN_SPOT = 'clean_spot';
    case CLEAN_ZONE = 'clean_zone';
    case SET_VACUUM_FAN_SPEED = 'set_vacuum_fan_speed';

    // ==========================================
    // JARDIN / IRRIGATION - NOUVEAU
    // ==========================================

    case START_WATERING = 'start_watering';
    case STOP_WATERING = 'stop_watering';
    case SET_WATERING_DURATION = 'set_watering_duration';
    case SET_WATERING_SCHEDULE = 'set_watering_schedule';

    // ==========================================
    // NOTIFICATION
    // ==========================================

    case SEND_NOTIFICATION = 'send_notification';
    case PLAY_SOUND = 'play_sound';
    case FLASH_LIGHT = 'flash_light';

    // ==========================================
    // SCÈNES - NOUVEAU
    // ==========================================

    case ACTIVATE_SCENE = 'activate_scene';
    case STORE_SCENE = 'store_scene';
    case RECALL_SCENE = 'recall_scene';
    case DELETE_SCENE = 'delete_scene';

    // ==========================================
    // DEVICES VIRTUELS - NOUVEAU
    // ==========================================

    case TRIGGER_WEBHOOK = 'trigger_webhook';
    case SEND_HTTP_REQUEST = 'send_http_request';
    case EXECUTE_SCRIPT = 'execute_script';
    case TRIGGER_AUTOMATION = 'trigger_automation';
    case INCREMENT_COUNTER = 'increment_counter';
    case DECREMENT_COUNTER = 'decrement_counter';
    case RESET_COUNTER = 'reset_counter';
    case START_TIMER = 'start_timer';
    case STOP_TIMER = 'stop_timer';
    case RESET_TIMER = 'reset_timer';

    // ==========================================
    // SYSTÈME
    // ==========================================

    case CONFIGURE = 'configure';
    case RESET_CONFIGURATION = 'reset_configuration';
    case CHECK_UPDATE = 'check_update';
    case START_UPDATE = 'start_update';
    case IDENTIFY = 'identify';

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

    public function requiresParameters(): bool
    {
        return match($this) {
            // Actions SANS paramètres
            self::TURN_ON,
            self::TURN_OFF,
            self::TOGGLE,
            self::INCREASE_BRIGHTNESS,
            self::DECREASE_BRIGHTNESS,
            self::SET_WARM_WHITE,
            self::SET_COOL_WHITE,
            self::SET_NEUTRAL_WHITE,
            self::STOP_EFFECT,
            self::BLINK,
            self::BREATHE,
            self::LOCK,
            self::UNLOCK,
            self::OPEN,
            self::CLOSE,
            self::STOP,
            self::ARM,
            self::ARM_AWAY,
            self::ARM_HOME,
            self::ARM_NIGHT,
            self::DISARM,
            self::TRIGGER,
            self::START_STREAMING,
            self::STOP_STREAMING,
            self::START_RECORDING,
            self::STOP_RECORDING,
            self::TAKE_PICTURE,
            self::PAN_LEFT,
            self::PAN_RIGHT,
            self::TILT_UP,
            self::TILT_DOWN,
            self::ZOOM_IN,
            self::ZOOM_OUT,
            self::SET_MODE_OFF,
            self::SET_MODE_HEAT,
            self::SET_MODE_COOL,
            self::SET_MODE_AUTO,
            self::INCREASE_HEATING_SETPOINT,
            self::DECREASE_HEATING_SETPOINT,
            self::INCREASE_COOLING_SETPOINT,
            self::DECREASE_COOLING_SETPOINT,
            self::SET_FAN_AUTO,
            self::SET_FAN_ON,
            self::INCREASE_FAN_SPEED,
            self::DECREASE_FAN_SPEED,
            self::ENABLE_OSCILLATION,
            self::DISABLE_OSCILLATION,
            self::INCREASE_HUMIDITY_TARGET,
            self::DECREASE_HUMIDITY_TARGET,
            self::INCREASE_PURIFIER_SPEED,
            self::DECREASE_PURIFIER_SPEED,
            self::OPEN_TILT,
            self::CLOSE_TILT,
            self::OPEN_VALVE,
            self::CLOSE_VALVE,
            self::INCREASE_VOLUME,
            self::DECREASE_VOLUME,
            self::VOLUME_UP,
            self::VOLUME_DOWN,
            self::MUTE,
            self::UNMUTE,
            self::TOGGLE_MUTE,
            self::PLAY,
            self::PAUSE,
            self::PLAY_PAUSE,
            self::STOP_MEDIA,
            self::NEXT_TRACK,
            self::PREVIOUS_TRACK,
            self::ENABLE_REPEAT,
            self::DISABLE_REPEAT,
            self::ENABLE_SHUFFLE,
            self::DISABLE_SHUFFLE,
            self::NEXT_INPUT,
            self::PREVIOUS_INPUT,
            self::CHANNEL_UP,
            self::CHANNEL_DOWN,
            self::START_CLEANING,
            self::PAUSE_CLEANING,
            self::STOP_CLEANING,
            self::RETURN_TO_BASE,
            self::LOCATE_VACUUM,
            self::CLEAN_SPOT,
            self::START_WATERING,
            self::STOP_WATERING,
            self::RESET_ENERGY,
            self::INCREMENT_COUNTER,
            self::DECREMENT_COUNTER,
            self::RESET_COUNTER,
            self::STOP_TIMER,
            self::RESET_TIMER,
            self::RESET_CONFIGURATION,
            self::CHECK_UPDATE,
            self::START_UPDATE,
            self::IDENTIFY
            => false,

            // Toutes les autres actions nécessitent des paramètres
            default => true,
        };
    }

    /**
     * Retourne les actions supportées par une capability donnée
     *
     * @return self[]
     */
    public static function getActionsForCapability(Capability $capability): array
    {
        return match($capability) {
            // ==========================================
            // ÉCLAIRAGE
            // ==========================================

            Capability::SWITCH,
            Capability::LIGHT => [
                self::TURN_ON,
                self::TURN_OFF,
                self::TOGGLE,
            ],

            Capability::BRIGHTNESS => [
                self::SET_BRIGHTNESS,
                self::INCREASE_BRIGHTNESS,
                self::DECREASE_BRIGHTNESS,
            ],

            Capability::COLOR_TEMPERATURE => [
                self::SET_COLOR_TEMPERATURE,
                self::SET_COLOR_TEMPERATURE_MIRED,
                self::SET_WARM_WHITE,
                self::SET_COOL_WHITE,
                self::SET_NEUTRAL_WHITE,
            ],

            Capability::COLOR_CONTROL => [
                self::SET_COLOR,
                self::SET_COLOR_RGB,
                self::SET_COLOR_HSV,
                self::SET_COLOR_XY,
                self::SET_COLOR_HEX,
                self::SET_HUE,
                self::SET_SATURATION,
            ],

            Capability::LIGHT_EFFECT => [
                self::SET_EFFECT,
                self::START_COLOR_LOOP,
                self::STOP_EFFECT,
                self::BLINK,
                self::BREATHE,
            ],

            // ==========================================
            // ÉNERGIE
            // ==========================================

            Capability::ENERGY_METER => [
                self::RESET_ENERGY,
            ],

            // ==========================================
            // SÉCURITÉ
            // ==========================================

            Capability::LOCK => [
                self::LOCK,
                self::UNLOCK,
                self::UNLOCK_WITH_TIMEOUT,
            ],

            Capability::DOOR_CONTROL => [
                self::OPEN,
                self::CLOSE,
                self::STOP,
            ],

            Capability::ALARM,
            Capability::SECURITY_SYSTEM => [
                self::ARM,
                self::ARM_AWAY,
                self::ARM_HOME,
                self::ARM_NIGHT,
                self::DISARM,
                self::TRIGGER,
            ],

            Capability::CAMERA => [
                self::START_STREAMING,
                self::STOP_STREAMING,
                self::START_RECORDING,
                self::STOP_RECORDING,
                self::TAKE_PICTURE,
                self::PAN_LEFT,
                self::PAN_RIGHT,
                self::TILT_UP,
                self::TILT_DOWN,
                self::ZOOM_IN,
                self::ZOOM_OUT,
                self::GO_TO_PRESET,
            ],

            // ==========================================
            // CLIMAT
            // ==========================================

            Capability::THERMOSTAT_MODE => [
                self::SET_THERMOSTAT_MODE,
                self::SET_MODE_OFF,
                self::SET_MODE_HEAT,
                self::SET_MODE_COOL,
                self::SET_MODE_AUTO,
            ],

            Capability::THERMOSTAT_HEATING_SETPOINT => [
                self::SET_HEATING_SETPOINT,
                self::INCREASE_HEATING_SETPOINT,
                self::DECREASE_HEATING_SETPOINT,
            ],

            Capability::THERMOSTAT_COOLING_SETPOINT => [
                self::SET_COOLING_SETPOINT,
                self::INCREASE_COOLING_SETPOINT,
                self::DECREASE_COOLING_SETPOINT,
            ],

            Capability::THERMOSTAT_FAN_MODE,
            Capability::FAN_MODE => [
                self::SET_FAN_MODE,
                self::SET_FAN_AUTO,
                self::SET_FAN_ON,
            ],

            Capability::FAN_SPEED => [
                self::SET_FAN_SPEED,
                self::SET_FAN_SPEED_PERCENT,
                self::INCREASE_FAN_SPEED,
                self::DECREASE_FAN_SPEED,
            ],

            Capability::FAN_OSCILLATION => [
                self::SET_OSCILLATION,
                self::ENABLE_OSCILLATION,
                self::DISABLE_OSCILLATION,
            ],

            Capability::HUMIDIFIER,
            Capability::DEHUMIDIFIER,
            Capability::HUMIDITY_CONTROL => [
                self::TURN_ON,
                self::TURN_OFF,
                self::SET_HUMIDITY_TARGET,
                self::INCREASE_HUMIDITY_TARGET,
                self::DECREASE_HUMIDITY_TARGET,
            ],

            Capability::AIR_PURIFIER => [
                self::TURN_ON,
                self::TURN_OFF,
            ],

            Capability::AIR_PURIFIER_MODE => [
                self::SET_PURIFIER_MODE,
                self::SET_PURIFIER_SPEED,
                self::INCREASE_PURIFIER_SPEED,
                self::DECREASE_PURIFIER_SPEED,
            ],

            // ==========================================
            // COUVERTURES / VOLETS
            // ==========================================

            Capability::WINDOW_COVERING => [
                self::OPEN,
                self::CLOSE,
                self::STOP,
            ],

            Capability::WINDOW_COVERING_POSITION => [
                self::SET_POSITION,
                self::OPEN_TO_POSITION,
                self::CLOSE_TO_POSITION,
            ],

            Capability::WINDOW_COVERING_TILT => [
                self::SET_TILT,
                self::OPEN_TILT,
                self::CLOSE_TILT,
            ],

            Capability::VALVE => [
                self::OPEN_VALVE,
                self::CLOSE_VALVE,
                self::SET_VALVE_POSITION,
            ],

            // ==========================================
            // AUDIO / VIDÉO
            // ==========================================

            Capability::AUDIO_VOLUME => [
                self::SET_VOLUME,
                self::INCREASE_VOLUME,
                self::DECREASE_VOLUME,
                self::VOLUME_UP,
                self::VOLUME_DOWN,
            ],

            Capability::AUDIO_MUTE => [
                self::MUTE,
                self::UNMUTE,
                self::TOGGLE_MUTE,
            ],

            Capability::MEDIA_PLAYBACK => [
                self::PLAY,
                self::PAUSE,
                self::PLAY_PAUSE,
                self::STOP_MEDIA,
            ],

            Capability::MEDIA_TRACK_CONTROL => [
                self::NEXT_TRACK,
                self::PREVIOUS_TRACK,
                self::SKIP_FORWARD,
                self::SKIP_BACKWARD,
            ],

            Capability::MEDIA_REPEAT => [
                self::SET_REPEAT_MODE,
                self::ENABLE_REPEAT,
                self::DISABLE_REPEAT,
            ],

            Capability::MEDIA_SHUFFLE => [
                self::SET_SHUFFLE,
                self::ENABLE_SHUFFLE,
                self::DISABLE_SHUFFLE,
            ],

            Capability::MEDIA_SEEK => [
                self::SEEK_TO_POSITION,
                self::SEEK_FORWARD,
                self::SEEK_BACKWARD,
            ],

            Capability::INPUT_SOURCE => [
                self::SET_INPUT_SOURCE,
                self::NEXT_INPUT,
                self::PREVIOUS_INPUT,
            ],

            Capability::TV_CHANNEL => [
                self::SET_CHANNEL,
                self::CHANNEL_UP,
                self::CHANNEL_DOWN,
            ],

            Capability::SPEAKER => [
                self::TURN_ON,
                self::TURN_OFF,
                self::SET_VOLUME,
                self::VOLUME_UP,
                self::VOLUME_DOWN,
                self::MUTE,
                self::UNMUTE,
            ],

            // ==========================================
            // NETTOYAGE
            // ==========================================

            Capability::VACUUM_CONTROL => [
                self::START_CLEANING,
                self::PAUSE_CLEANING,
                self::STOP_CLEANING,
                self::RETURN_TO_BASE,
                self::LOCATE_VACUUM,
                self::CLEAN_SPOT,
            ],

            Capability::VACUUM_FAN_SPEED => [
                self::SET_VACUUM_FAN_SPEED,
            ],

            Capability::VACUUM_ZONE => [
                self::CLEAN_ZONE,
            ],

            // ==========================================
            // JARDIN / IRRIGATION
            // ==========================================

            Capability::SPRINKLER,
            Capability::IRRIGATION_CONTROL => [
                self::START_WATERING,
                self::STOP_WATERING,
                self::SET_WATERING_DURATION,
                self::SET_WATERING_SCHEDULE,
            ],

            // ==========================================
            // NOTIFICATION
            // ==========================================

            Capability::NOTIFICATION => [
                self::SEND_NOTIFICATION,
            ],

            Capability::INDICATOR => [
                self::FLASH_LIGHT,
                self::PLAY_SOUND,
            ],

            // ==========================================
            // SCÈNES
            // ==========================================

            Capability::SCENE_CONTROL => [
                self::ACTIVATE_SCENE,
                self::STORE_SCENE,
                self::RECALL_SCENE,
                self::DELETE_SCENE,
            ],

            // ==========================================
            // DEVICES VIRTUELS
            // ==========================================

            Capability::WEBHOOK => [
                self::TRIGGER_WEBHOOK,
            ],

            Capability::HTTP_REQUEST => [
                self::SEND_HTTP_REQUEST,
            ],

            Capability::SCRIPT_EXECUTION => [
                self::EXECUTE_SCRIPT,
            ],

            Capability::AUTOMATION_TRIGGER => [
                self::TRIGGER_AUTOMATION,
            ],

            Capability::COUNTER => [
                self::INCREMENT_COUNTER,
                self::DECREMENT_COUNTER,
                self::RESET_COUNTER,
            ],

            Capability::TIMER => [
                self::START_TIMER,
                self::STOP_TIMER,
                self::RESET_TIMER,
            ],

            // ==========================================
            // SYSTÈME
            // ==========================================

            Capability::CONFIGURATION => [
                self::CONFIGURE,
                self::RESET_CONFIGURATION,
            ],

            Capability::UPDATE => [
                self::CHECK_UPDATE,
                self::START_UPDATE,
            ],

            Capability::IDENTIFY => [
                self::IDENTIFY,
            ],

            // ==========================================
            // CAPABILITIES COMPOSITES
            // Déléguation aux capabilities de base
            // ==========================================

            Capability::THERMOSTAT => array_merge(
                self::getActionsForCapability(Capability::THERMOSTAT_MODE),
                self::getActionsForCapability(Capability::THERMOSTAT_HEATING_SETPOINT),
                self::getActionsForCapability(Capability::THERMOSTAT_COOLING_SETPOINT),
                self::getActionsForCapability(Capability::THERMOSTAT_FAN_MODE),
            ),

            Capability::RGBW_LIGHT => array_merge(
                self::getActionsForCapability(Capability::LIGHT),
                self::getActionsForCapability(Capability::BRIGHTNESS),
                self::getActionsForCapability(Capability::COLOR_CONTROL),
                self::getActionsForCapability(Capability::COLOR_TEMPERATURE),
                self::getActionsForCapability(Capability::LIGHT_EFFECT),
            ),

            Capability::SMART_PLUG => array_merge(
                self::getActionsForCapability(Capability::SWITCH),
                self::getActionsForCapability(Capability::ENERGY_METER),
            ),

            Capability::SMART_LOCK => array_merge(
                self::getActionsForCapability(Capability::LOCK),
            ),

            Capability::SMART_FAN => array_merge(
                self::getActionsForCapability(Capability::SWITCH),
                self::getActionsForCapability(Capability::FAN_SPEED),
                self::getActionsForCapability(Capability::FAN_OSCILLATION),
            ),

            Capability::MOTORIZED_BLIND => array_merge(
                self::getActionsForCapability(Capability::WINDOW_COVERING),
                self::getActionsForCapability(Capability::WINDOW_COVERING_POSITION),
                self::getActionsForCapability(Capability::WINDOW_COVERING_TILT),
            ),

            Capability::SMART_THERMOSTAT_VALVE => array_merge(
                self::getActionsForCapability(Capability::THERMOSTAT_HEATING_SETPOINT),
            ),

            // ==========================================
            // CAPABILITIES SANS ACTIONS
            // (capteurs en lecture seule, états, etc.)
            // ==========================================

            default => [],
        };
    }

    public function getSupportingCapabilities(): array
    {
        $capabilities = [];

        foreach (Capability::cases() as $capability) {
            $actions = self::getActionsForCapability($capability);
            if (in_array($this, $actions, true)) {
                $capabilities[] = $capability;
            }
        }

        return $capabilities;
    }

}
