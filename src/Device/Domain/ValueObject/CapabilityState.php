<?php

namespace Marvin\Device\Domain\ValueObject;

use Marvin\Device\Domain\Exception\CapabilityStateDataTypeNotImplemented;
use Marvin\Device\Domain\Specification\CapabilityStateConstraints;

/**
 * CapabilityState - États/mesures possibles pour les capabilities
 *
 * Représente tous les states qu'un device peut exposer.
 * Les states sont en lecture (capteurs) ou lecture/écriture (actionneurs).
 *
 * Total : ~150 states
 */
enum CapabilityState: string
{
    // ==========================================
    // ÉCLAIRAGE (12)
    // ==========================================

    case STATE = 'state'; // on/off générique
    case BRIGHTNESS = 'brightness'; // 0-100
    case COLOR_TEMPERATURE = 'color_temperature'; // Kelvin
    case COLOR_TEMPERATURE_MIRED = 'color_temperature_mired'; // Mired
    case COLOR_MODE = 'color_mode'; // xy, hs, temp
    case COLOR_RGB = 'color_rgb'; // {r, g, b}
    case COLOR_HSV = 'color_hsv'; // {h, s, v}
    case COLOR_XY = 'color_xy'; // {x, y}
    case HUE = 'hue'; // 0-360
    case SATURATION = 'saturation'; // 0-100
    case EFFECT = 'effect'; // blink, breathe, etc.
    case TRANSITION = 'transition'; // durée transition ms

    // ==========================================
    // ÉNERGIE (10)
    // ==========================================

    case POWER = 'power'; // Watts (instantané)
    case ENERGY = 'energy'; // kWh (cumulé)
    case VOLTAGE = 'voltage'; // Volts
    case CURRENT = 'current'; // Ampères
    case POWER_FACTOR = 'power_factor'; // 0-1
    case APPARENT_POWER = 'apparent_power'; // VA
    case REACTIVE_POWER = 'reactive_power'; // VAR
    case POWER_SOURCE = 'power_source'; // mains, battery, dc
    case BATTERY_PERCENTAGE = 'battery_percentage'; // 0-100
    case BATTERY_VOLTAGE = 'battery_voltage'; // Volts

    // ==========================================
    // CAPTEURS ENVIRONNEMENTAUX (15)
    // ==========================================

    case TEMPERATURE = 'temperature'; // °C
    case HUMIDITY = 'humidity'; // % RH
    case PRESSURE = 'pressure'; // hPa
    case AIR_QUALITY_INDEX = 'air_quality_index'; // AQI 0-500
    case AIR_QUALITY = 'air_quality'; // good, moderate, poor
    case CO2 = 'co2'; // ppm
    case CO2_LEVEL = 'co2_level'; // normal, warning, critical
    case CO = 'co'; // ppm
    case CO_DETECTED = 'co_detected'; // bool
    case VOC = 'voc'; // ppb
    case PM25 = 'pm25'; // µg/m³
    case PM10 = 'pm10'; // µg/m³
    case ILLUMINANCE = 'illuminance'; // lux
    case UV_INDEX = 'uv_index'; // 0-11+
    case NOISE_LEVEL = 'noise_level'; // dB

    // ==========================================
    // SÉCURITÉ - DÉTECTION (20)
    // ==========================================

    case MOTION = 'motion'; // detected/clear
    case MOTION_DETECTED = 'motion_detected'; // bool
    case LAST_MOTION = 'last_motion'; // datetime
    case OCCUPANCY = 'occupancy'; // occupied/unoccupied
    case OCCUPANCY_DETECTED = 'occupancy_detected'; // bool
    case LAST_OCCUPIED = 'last_occupied'; // datetime
    case CONTACT = 'contact'; // open/closed
    case CONTACT_OPEN = 'contact_open'; // bool
    case LAST_OPENED = 'last_opened'; // datetime
    case LAST_CLOSED = 'last_closed'; // datetime
    case VIBRATION = 'vibration'; // detected/clear
    case VIBRATION_DETECTED = 'vibration_detected'; // bool
    case WATER_LEAK = 'water_leak'; // detected/clear
    case WATER_LEAK_DETECTED = 'water_leak_detected'; // bool
    case SMOKE = 'smoke'; // detected/clear
    case SMOKE_DETECTED = 'smoke_detected'; // bool
    case GLASS_BREAK = 'glass_break'; // detected/clear
    case GLASS_BREAK_DETECTED = 'glass_break_detected'; // bool
    case TAMPER = 'tamper'; // tampered/clear
    case TAMPER_DETECTED = 'tamper_detected'; // bool

    // ==========================================
    // SÉCURITÉ - CONTRÔLE (10)
    // ==========================================

    case LOCK_STATE = 'lock_state'; // locked/unlocked
    case IS_LOCKED = 'is_locked'; // bool
    case DOOR_STATE = 'door_state'; // open/closed/opening/closing
    case IS_DOOR_OPEN = 'is_door_open'; // bool
    case ALARM_STATE = 'alarm_state'; // armed_away/armed_home/armed_night/disarmed
    case IS_ARMED = 'is_armed'; // bool
    case ALARM_TRIGGERED = 'alarm_triggered'; // bool
    case SECURITY_SYSTEM_STATE = 'security_system_state'; // armed/disarmed/triggered
    case INTRUSION_DETECTED = 'intrusion_detected'; // bool
    case LAST_INTRUSION = 'last_intrusion'; // datetime

    // ==========================================
    // CAMÉRA (15)
    // ==========================================

    case CAMERA_STATE = 'camera_state'; // streaming/recording/idle
    case IS_STREAMING = 'is_streaming'; // bool
    case IS_RECORDING = 'is_recording'; // bool
    case STREAM_URL = 'stream_url'; // URL
    case SNAPSHOT_URL = 'snapshot_url'; // URL
    case PAN_POSITION = 'pan_position'; // -180 to 180
    case TILT_POSITION = 'tilt_position'; // -90 to 90
    case ZOOM_LEVEL = 'zoom_level'; // 1-10
    case PRESET = 'preset'; // preset name
    case MOTION_DETECTION_ENABLED = 'motion_detection_enabled'; // bool
    case NIGHT_VISION_ENABLED = 'night_vision_enabled'; // bool
    case AUDIO_ENABLED = 'audio_enabled'; // bool
    case PRIVACY_MODE = 'privacy_mode'; // bool
    case RECORDING_DURATION = 'recording_duration'; // seconds
    case LAST_RECORDING = 'last_recording'; // datetime

    // ==========================================
    // CLIMAT - THERMOSTAT (15)
    // ==========================================

    case THERMOSTAT_MODE = 'thermostat_mode'; // off/heat/cool/auto/dry/fan_only
    case HVAC_MODE = 'hvac_mode'; // alias de thermostat_mode
    case HEATING_SETPOINT = 'heating_setpoint'; // °C
    case COOLING_SETPOINT = 'cooling_setpoint'; // °C
    case TARGET_TEMPERATURE = 'target_temperature'; // °C
    case CURRENT_TEMPERATURE = 'current_temperature'; // °C
    case THERMOSTAT_OPERATING_STATE = 'thermostat_operating_state'; // heating/cooling/idle
    case IS_HEATING = 'is_heating'; // bool
    case IS_COOLING = 'is_cooling'; // bool
    case FAN_MODE = 'fan_mode'; // auto/on/off/low/medium/high
    case FAN_STATE = 'fan_state'; // on/off
    case FAN_SPEED = 'fan_speed'; // 0-100 ou low/medium/high
    case FAN_SPEED_PERCENT = 'fan_speed_percent'; // 0-100
    case OSCILLATION = 'oscillation'; // on/off
    case IS_OSCILLATING = 'is_oscillating'; // bool

    // ==========================================
    // CLIMAT - HUMIDITÉ & AIR (8)
    // ==========================================

    case HUMIDITY_SETPOINT = 'humidity_setpoint'; // %
    case TARGET_HUMIDITY = 'target_humidity'; // %
    case CURRENT_HUMIDITY = 'current_humidity'; // %
    case HUMIDIFIER_STATE = 'humidifier_state'; // on/off
    case DEHUMIDIFIER_STATE = 'dehumidifier_state'; // on/off
    case PURIFIER_MODE = 'purifier_mode'; // auto/sleep/low/medium/high
    case PURIFIER_STATE = 'purifier_state'; // on/off
    case FILTER_LIFE_REMAINING = 'filter_life_remaining'; // 0-100%

    // ==========================================
    // COUVERTURES / VOLETS (10)
    // ==========================================

    case WINDOW_COVERING_STATE = 'window_covering_state'; // open/closed/opening/closing/stopped
    case IS_COVER_OPEN = 'is_cover_open'; // bool
    case IS_COVER_CLOSED = 'is_cover_closed'; // bool
    case POSITION = 'position'; // 0-100% (0=closed, 100=open)
    case TILT = 'tilt'; // 0-100%
    case TILT_ANGLE = 'tilt_angle'; // -90 to 90 degrees
    case VALVE_STATE = 'valve_state'; // open/closed/opening/closing
    case IS_VALVE_OPEN = 'is_valve_open'; // bool
    case VALVE_POSITION = 'valve_position'; // 0-100%
    case FLOW_RATE = 'flow_rate'; // L/min

    // ==========================================
    // AUDIO / VIDÉO (20)
    // ==========================================

    case VOLUME = 'volume'; // 0-100
    case VOLUME_LEVEL = 'volume_level'; // 0-100
    case IS_MUTED = 'is_muted'; // bool
    case MUTE_STATE = 'mute_state'; // muted/unmuted
    case PLAYBACK_STATE = 'playback_state'; // playing/paused/stopped
    case IS_PLAYING = 'is_playing'; // bool
    case MEDIA_TITLE = 'media_title'; // string
    case MEDIA_ARTIST = 'media_artist'; // string
    case MEDIA_ALBUM = 'media_album'; // string
    case MEDIA_DURATION = 'media_duration'; // seconds
    case MEDIA_POSITION = 'media_position'; // seconds
    case MEDIA_PROGRESS = 'media_progress'; // 0-100%
    case MEDIA_IMAGE_URL = 'media_image_url'; // URL
    case REPEAT_MODE = 'repeat_mode'; // off/one/all
    case IS_REPEAT = 'is_repeat'; // bool
    case SHUFFLE_MODE = 'shuffle_mode'; // on/off
    case IS_SHUFFLE = 'is_shuffle'; // bool
    case INPUT_SOURCE = 'input_source'; // HDMI1, HDMI2, etc.
    case CHANNEL = 'channel'; // channel number or name
    case CHANNEL_NAME = 'channel_name'; // string

    // ==========================================
    // CONTRÔLE / BOUTONS (8)
    // ==========================================

    case BUTTON_STATE = 'button_state'; // single/double/long/release
    case BUTTON_EVENT = 'button_event'; // événement éphémère
    case LAST_BUTTON_EVENT = 'last_button_event'; // datetime
    case BUTTON_NUMBER = 'button_number'; // 1-n pour multi-button
    case ROTATION = 'rotation'; // left/right
    case ROTATION_ANGLE = 'rotation_angle'; // degrees
    case INDICATOR_STATE = 'indicator_state'; // on/off
    case INDICATOR_COLOR = 'indicator_color'; // color

    // ==========================================
    // MESURE / RÉSEAU (8)
    // ==========================================

    case BATTERY = 'battery'; // 0-100%
    case BATTERY_LOW = 'battery_low'; // bool
    case BATTERY_CHARGING = 'battery_charging'; // bool
    case SIGNAL_STRENGTH = 'signal_strength'; // dBm ou %
    case RSSI = 'rssi'; // dBm
    case LINK_QUALITY = 'link_quality'; // 0-255
    case NETWORK_STATUS = 'network_status'; // online/offline
    case IS_REACHABLE = 'is_reachable'; // bool

    // ==========================================
    // VACUUM (ASPIRATEUR) (12)
    // ==========================================

    case VACUUM_STATE = 'vacuum_state'; // cleaning/paused/returning/docked/error
    case IS_CLEANING = 'is_cleaning'; // bool
    case IS_DOCKED = 'is_docked'; // bool
    case VACUUM_FAN_SPEED = 'vacuum_fan_speed'; // low/medium/high/max
    case VACUUM_BATTERY = 'vacuum_battery'; // 0-100%
    case CLEANED_AREA = 'cleaned_area'; // m²
    case CLEANING_TIME = 'cleaning_time'; // minutes
    case FILTER_REMAINING = 'filter_remaining'; // hours
    case BRUSH_REMAINING = 'brush_remaining'; // hours
    case SIDE_BRUSH_REMAINING = 'side_brush_remaining'; // hours
    case CURRENT_ZONE = 'current_zone'; // zone name
    case ERROR_CODE = 'error_code'; // error code

    // ==========================================
    // JARDIN / IRRIGATION (8)
    // ==========================================

    case SPRINKLER_STATE = 'sprinkler_state'; // watering/idle
    case IS_WATERING = 'is_watering'; // bool
    case WATERING_DURATION = 'watering_duration'; // minutes
    case REMAINING_TIME = 'remaining_time'; // minutes
    case WATER_FLOW = 'water_flow'; // L/min
    case TOTAL_WATER_USED = 'total_water_used'; // L
    case SOIL_MOISTURE = 'soil_moisture'; // 0-100%
    case SOIL_TEMPERATURE = 'soil_temperature'; // °C

    // ==========================================
    // VIRTUELS - TEMPS (10)
    // ==========================================

    case CURRENT_TIME = 'current_time'; // HH:mm:ss
    case CURRENT_DATE = 'current_date'; // YYYY-MM-DD
    case CURRENT_DATETIME = 'current_datetime'; // ISO 8601
    case DAY_OF_WEEK = 'day_of_week'; // monday, tuesday, etc.
    case WEEK_NUMBER = 'week_number'; // 1-52
    case SUNRISE_TIME = 'sunrise_time'; // HH:mm
    case SUNSET_TIME = 'sunset_time'; // HH:mm
    case IS_DAY = 'is_day'; // bool
    case IS_NIGHT = 'is_night'; // bool
    case DAYLIGHT_DURATION = 'daylight_duration'; // minutes

    // ==========================================
    // VIRTUELS - COMPTEUR & TIMER (6)
    // ==========================================

    case COUNTER_VALUE = 'counter_value'; // int
    case COUNTER_MAX = 'counter_max'; // int
    case TIMER_STATE = 'timer_state'; // running/stopped
    case TIMER_REMAINING = 'timer_remaining'; // seconds
    case TIMER_ELAPSED = 'timer_elapsed'; // seconds
    case TIMER_DURATION = 'timer_duration'; // seconds

    // ==========================================
    // VIRTUELS - WEBHOOK & HTTP (5)
    // ==========================================

    case LAST_WEBHOOK_TRIGGER = 'last_webhook_trigger'; // datetime
    case WEBHOOK_RESPONSE = 'webhook_response'; // response body
    case HTTP_STATUS_CODE = 'http_status_code'; // 200, 404, etc.
    case HTTP_RESPONSE = 'http_response'; // response body
    case SCRIPT_OUTPUT = 'script_output'; // script output

    // ==========================================
    // SYSTÈME (10)
    // ==========================================

    case ONLINE = 'online'; // bool
    case LAST_SEEN = 'last_seen'; // datetime
    case FIRMWARE_VERSION = 'firmware_version'; // string
    case UPDATE_AVAILABLE = 'update_available'; // bool
    case UPDATE_PROGRESS = 'update_progress'; // 0-100%
    case UPTIME = 'uptime'; // seconds
    case HEALTH_STATUS = 'health_status'; // healthy/warning/error
    case LAST_ERROR = 'last_error'; // error message
    case DEVICE_TEMPERATURE = 'device_temperature'; // °C (température du device lui-même)
    case MEMORY_USAGE = 'memory_usage'; // 0-100%

    public function getDataType(): CapabilityStateDataType
    {
        return match ($this) {
            // Boolean
            self::MOTION_DETECTED,
            self::OCCUPANCY_DETECTED,
            self::CONTACT_OPEN,
            self::VIBRATION_DETECTED,
            self::WATER_LEAK_DETECTED,
            self::SMOKE_DETECTED,
            self::GLASS_BREAK_DETECTED,
            self::TAMPER_DETECTED,
            self::IS_LOCKED,
            self::IS_DOOR_OPEN,
            self::IS_ARMED,
            self::ALARM_TRIGGERED,
            self::INTRUSION_DETECTED,
            self::IS_STREAMING,
            self::IS_RECORDING,
            self::MOTION_DETECTION_ENABLED,
            self::NIGHT_VISION_ENABLED,
            self::AUDIO_ENABLED,
            self::PRIVACY_MODE,
            self::IS_HEATING,
            self::IS_COOLING,
            self::IS_OSCILLATING,
            self::IS_COVER_OPEN,
            self::IS_COVER_CLOSED,
            self::IS_VALVE_OPEN,
            self::IS_MUTED,
            self::IS_PLAYING,
            self::IS_REPEAT,
            self::IS_SHUFFLE,
            self::IS_CLEANING,
            self::IS_DOCKED,
            self::IS_WATERING,
            self::IS_DAY,
            self::IS_NIGHT,
            self::ONLINE,
            self::UPDATE_AVAILABLE,
            self::BATTERY_LOW,
            self::BATTERY_CHARGING,
            self::IS_REACHABLE => CapabilityStateDataType::BOOLEAN,

            // Integer
            self::BRIGHTNESS,
            self::COLOR_TEMPERATURE,
            self::COLOR_TEMPERATURE_MIRED,
            self::HUE,
            self::SATURATION,
            self::TRANSITION,
            self::POWER,
            self::VOLTAGE,
            self::CURRENT,
            self::APPARENT_POWER,
            self::REACTIVE_POWER,
            self::BATTERY_PERCENTAGE,
            self::BATTERY_VOLTAGE,
            self::AIR_QUALITY_INDEX,
            self::CO2,
            self::CO,
            self::VOC,
            self::PM25,
            self::PM10,
            self::ILLUMINANCE,
            self::UV_INDEX,
            self::NOISE_LEVEL,
            self::PAN_POSITION,
            self::TILT_POSITION,
            self::ZOOM_LEVEL,
            self::POSITION,
            self::TILT,
            self::TILT_ANGLE,
            self::VALVE_POSITION,
            self::VOLUME,
            self::VOLUME_LEVEL,
            self::MEDIA_DURATION,
            self::MEDIA_POSITION,
            self::MEDIA_PROGRESS,
            self::ROTATION_ANGLE,
            self::SIGNAL_STRENGTH,
            self::RSSI,
            self::LINK_QUALITY,
            self::VACUUM_BATTERY,
            self::CLEANED_AREA,
            self::CLEANING_TIME,
            self::FILTER_REMAINING,
            self::BRUSH_REMAINING,
            self::SIDE_BRUSH_REMAINING,
            self::WATERING_DURATION,
            self::REMAINING_TIME,
            self::TOTAL_WATER_USED,
            self::SOIL_MOISTURE,
            self::WEEK_NUMBER,
            self::DAYLIGHT_DURATION,
            self::COUNTER_VALUE,
            self::COUNTER_MAX,
            self::TIMER_REMAINING,
            self::TIMER_ELAPSED,
            self::TIMER_DURATION,
            self::HTTP_STATUS_CODE,
            self::UPDATE_PROGRESS,
            self::UPTIME,
            self::MEMORY_USAGE,
            self::RECORDING_DURATION,
            self::BUTTON_NUMBER,
            self::CHANNEL,
            self::FAN_SPEED_PERCENT,
            self::FILTER_LIFE_REMAINING => CapabilityStateDataType::INTEGER,

            // Float
            self::TEMPERATURE,
            self::HUMIDITY,
            self::PRESSURE,
            self::ENERGY,
            self::POWER_FACTOR,
            self::HEATING_SETPOINT,
            self::COOLING_SETPOINT,
            self::TARGET_TEMPERATURE,
            self::CURRENT_TEMPERATURE,
            self::HUMIDITY_SETPOINT,
            self::TARGET_HUMIDITY,
            self::CURRENT_HUMIDITY,
            self::FLOW_RATE,
            self::WATER_FLOW,
            self::SOIL_TEMPERATURE,
            self::DEVICE_TEMPERATURE => CapabilityStateDataType::FLOAT,

            // String
            self::STATE,
            self::COLOR_MODE,
            self::EFFECT,
            self::POWER_SOURCE,
            self::AIR_QUALITY,
            self::CO2_LEVEL,
            self::MOTION,
            self::OCCUPANCY,
            self::CONTACT,
            self::VIBRATION,
            self::WATER_LEAK,
            self::SMOKE,
            self::GLASS_BREAK,
            self::TAMPER,
            self::LOCK_STATE,
            self::DOOR_STATE,
            self::ALARM_STATE,
            self::SECURITY_SYSTEM_STATE,
            self::CAMERA_STATE,
            self::STREAM_URL,
            self::SNAPSHOT_URL,
            self::PRESET,
            self::THERMOSTAT_MODE,
            self::HVAC_MODE,
            self::THERMOSTAT_OPERATING_STATE,
            self::FAN_MODE,
            self::FAN_STATE,
            self::FAN_SPEED,
            self::HUMIDIFIER_STATE,
            self::DEHUMIDIFIER_STATE,
            self::PURIFIER_MODE,
            self::PURIFIER_STATE,
            self::WINDOW_COVERING_STATE,
            self::VALVE_STATE,
            self::MUTE_STATE,
            self::PLAYBACK_STATE,
            self::MEDIA_TITLE,
            self::MEDIA_ARTIST,
            self::MEDIA_ALBUM,
            self::MEDIA_IMAGE_URL,
            self::REPEAT_MODE,
            self::SHUFFLE_MODE,
            self::INPUT_SOURCE,
            self::CHANNEL_NAME,
            self::BUTTON_STATE,
            self::BUTTON_EVENT,
            self::ROTATION,
            self::INDICATOR_STATE,
            self::INDICATOR_COLOR,
            self::NETWORK_STATUS,
            self::VACUUM_STATE,
            self::VACUUM_FAN_SPEED,
            self::CURRENT_ZONE,
            self::ERROR_CODE,
            self::SPRINKLER_STATE,
            self::CURRENT_TIME,
            self::CURRENT_DATE,
            self::CURRENT_DATETIME,
            self::DAY_OF_WEEK,
            self::SUNRISE_TIME,
            self::SUNSET_TIME,
            self::TIMER_STATE,
            self::WEBHOOK_RESPONSE,
            self::HTTP_RESPONSE,
            self::SCRIPT_OUTPUT,
            self::FIRMWARE_VERSION,
            self::HEALTH_STATUS,
            self::LAST_ERROR => CapabilityStateDataType::STRING,

            // DateTime
            self::LAST_MOTION,
            self::LAST_OCCUPIED,
            self::LAST_OPENED,
            self::LAST_CLOSED,
            self::LAST_INTRUSION,
            self::LAST_RECORDING,
            self::LAST_BUTTON_EVENT,
            self::LAST_WEBHOOK_TRIGGER,
            self::LAST_SEEN => CapabilityStateDataType::DATETIME,

            // Object/Array
            self::COLOR_RGB,
            self::COLOR_HSV,
            self::COLOR_XY => CapabilityStateDataType::OBJECT,
            self::CO_DETECTED => throw CapabilityStateDataTypeNotImplemented::withType(self::CO_DETECTED),
            self::OSCILLATION => throw CapabilityStateDataTypeNotImplemented::withType(self::OSCILLATION),
            self::BATTERY => throw CapabilityStateDataTypeNotImplemented::withType(self::BATTERY),
        };
    }

    public function getUnit(): ?string
    {
        return match ($this) {
            self::BRIGHTNESS,
            self::SATURATION,
            self::BATTERY_PERCENTAGE,
            self::POSITION,
            self::TILT,
            self::VALVE_POSITION,
            self::VOLUME,
            self::VOLUME_LEVEL,
            self::MEDIA_PROGRESS,
            self::HUMIDITY,
            self::TARGET_HUMIDITY,
            self::CURRENT_HUMIDITY,
            self::SOIL_MOISTURE,
            self::FILTER_LIFE_REMAINING,
            self::UPDATE_PROGRESS,
            self::MEMORY_USAGE,
            self::FAN_SPEED_PERCENT,
            self::VACUUM_BATTERY => '%',

            self::TEMPERATURE,
            self::HEATING_SETPOINT,
            self::COOLING_SETPOINT,
            self::TARGET_TEMPERATURE,
            self::CURRENT_TEMPERATURE,
            self::SOIL_TEMPERATURE,
            self::DEVICE_TEMPERATURE => '°C',

            self::COLOR_TEMPERATURE,
            self::POWER => 'K',

            self::ENERGY => 'kWh',

            self::VOLTAGE,
            self::BATTERY_VOLTAGE => 'V',

            self::CURRENT => 'A',

            self::APPARENT_POWER => 'VA',

            self::REACTIVE_POWER => 'VAR',

            self::PRESSURE => 'hPa',

            self::CO2,
            self::CO => 'ppm',

            self::VOC => 'ppb',

            self::PM25,
            self::PM10 => 'µg/m³',

            self::ILLUMINANCE => 'lux',

            self::NOISE_LEVEL => 'dB',

            self::PAN_POSITION,
            self::TILT_ANGLE,
            self::ROTATION_ANGLE => '°',

            self::FLOW_RATE,
            self::WATER_FLOW => 'L/min',

            self::TOTAL_WATER_USED => 'L',

            self::CLEANED_AREA => 'm²',

            self::SIGNAL_STRENGTH,
            self::RSSI => 'dBm',

            self::MEDIA_DURATION,
            self::MEDIA_POSITION,
            self::CLEANING_TIME,
            self::WATERING_DURATION,
            self::REMAINING_TIME,
            self::DAYLIGHT_DURATION,
            self::TIMER_REMAINING,
            self::TIMER_ELAPSED,
            self::TIMER_DURATION,
            self::UPTIME,
            self::RECORDING_DURATION => 's',

            self::FILTER_REMAINING,
            self::BRUSH_REMAINING,
            self::SIDE_BRUSH_REMAINING => 'h',

            default => null,
        };
    }

    public function isReadOnly(): bool
    {
        return match ($this) {
            // États calculés ou mesurés (lecture seule)
            self::CURRENT_TEMPERATURE,
            self::CURRENT_HUMIDITY,
            self::PRESSURE,
            self::AIR_QUALITY,
            self::AIR_QUALITY_INDEX,
            self::CO2,
            self::CO2_LEVEL,
            self::CO,
            self::CO_DETECTED,
            self::VOC,
            self::PM25,
            self::PM10,
            self::ILLUMINANCE,
            self::UV_INDEX,
            self::NOISE_LEVEL,
            self::MOTION,
            self::MOTION_DETECTED,
            self::LAST_MOTION,
            self::OCCUPANCY,
            self::OCCUPANCY_DETECTED,
            self::LAST_OCCUPIED,
            self::CONTACT,
            self::CONTACT_OPEN,
            self::LAST_OPENED,
            self::LAST_CLOSED,
            self::VIBRATION,
            self::VIBRATION_DETECTED,
            self::WATER_LEAK,
            self::WATER_LEAK_DETECTED,
            self::SMOKE,
            self::SMOKE_DETECTED,
            self::GLASS_BREAK,
            self::GLASS_BREAK_DETECTED,
            self::TAMPER,
            self::TAMPER_DETECTED,
            self::POWER,
            self::ENERGY,
            self::VOLTAGE,
            self::CURRENT,
            self::POWER_FACTOR,
            self::APPARENT_POWER,
            self::REACTIVE_POWER,
            self::BATTERY,
            self::BATTERY_PERCENTAGE,
            self::BATTERY_VOLTAGE,
            self::BATTERY_LOW,
            self::BATTERY_CHARGING,
            self::SIGNAL_STRENGTH,
            self::RSSI,
            self::LINK_QUALITY,
            self::NETWORK_STATUS,
            self::IS_REACHABLE,
            self::ONLINE,
            self::LAST_SEEN,
            self::THERMOSTAT_OPERATING_STATE,
            self::IS_HEATING,
            self::IS_COOLING,
            self::FILTER_LIFE_REMAINING,
            self::BUTTON_STATE,
            self::BUTTON_EVENT,
            self::LAST_BUTTON_EVENT,
            self::BUTTON_NUMBER,
            self::ROTATION,
            self::ROTATION_ANGLE,
            self::MEDIA_TITLE,
            self::MEDIA_ARTIST,
            self::MEDIA_ALBUM,
            self::MEDIA_DURATION,
            self::MEDIA_IMAGE_URL,
            self::CURRENT_TIME,
            self::CURRENT_DATE,
            self::CURRENT_DATETIME,
            self::DAY_OF_WEEK,
            self::WEEK_NUMBER,
            self::SUNRISE_TIME,
            self::SUNSET_TIME,
            self::IS_DAY,
            self::IS_NIGHT,
            self::DAYLIGHT_DURATION,
            self::FIRMWARE_VERSION,
            self::UPDATE_AVAILABLE,
            self::UPTIME,
            self::HEALTH_STATUS,
            self::LAST_ERROR,
            self::DEVICE_TEMPERATURE,
            self::MEMORY_USAGE,
            self::CLEANED_AREA,
            self::CLEANING_TIME,
            self::FILTER_REMAINING,
            self::BRUSH_REMAINING,
            self::SIDE_BRUSH_REMAINING,
            self::ERROR_CODE,
            self::SOIL_MOISTURE,
            self::SOIL_TEMPERATURE,
            self::FLOW_RATE,
            self::WATER_FLOW,
            self::LAST_INTRUSION,
            self::LAST_RECORDING,
            self::HTTP_STATUS_CODE,
            self::HTTP_RESPONSE,
            self::WEBHOOK_RESPONSE,
            self::SCRIPT_OUTPUT,
            self::LAST_WEBHOOK_TRIGGER
            => true,

            // Tous les autres sont en lecture/écriture
            default => false,
        };
    }

    /**
     * Retourne les states pour une capability donnée
     *
     * @return self[]
     */
    public static function getStatesForCapability(Capability $capability): array
    {
        return match ($capability) {
            Capability::SWITCH,
            Capability::LIGHT => [
                self::STATE,
            ],

            Capability::BRIGHTNESS => [
                self::BRIGHTNESS,
            ],

            Capability::COLOR_TEMPERATURE => [
                self::COLOR_TEMPERATURE,
                self::COLOR_TEMPERATURE_MIRED,
            ],

            Capability::COLOR_CONTROL => [
                self::COLOR_RGB,
                self::COLOR_HSV,
                self::COLOR_XY,
                self::HUE,
                self::SATURATION,
            ],

            Capability::COLOR_MODE => [
                self::COLOR_MODE,
            ],

            Capability::LIGHT_EFFECT => [
                self::EFFECT,
            ],

            Capability::POWER_METER => [
                self::POWER,
                self::APPARENT_POWER,
                self::REACTIVE_POWER,
            ],

            Capability::ENERGY_METER => [
                self::ENERGY,
            ],

            Capability::VOLTAGE_MEASUREMENT => [
                self::VOLTAGE,
            ],

            Capability::CURRENT_MEASUREMENT => [
                self::CURRENT,
            ],

            Capability::POWER_FACTOR => [
                self::POWER_FACTOR,
            ],

            Capability::POWER_SOURCE => [
                self::POWER_SOURCE,
            ],

            Capability::TEMPERATURE_MEASUREMENT => [
                self::TEMPERATURE,
            ],

            Capability::HUMIDITY_MEASUREMENT => [
                self::HUMIDITY,
            ],

            Capability::PRESSURE_MEASUREMENT => [
                self::PRESSURE,
            ],

            Capability::AIR_QUALITY => [
                self::AIR_QUALITY,
                self::AIR_QUALITY_INDEX,
            ],

            Capability::CARBON_DIOXIDE_MEASUREMENT => [
                self::CO2,
                self::CO2_LEVEL,
            ],

            Capability::CARBON_MONOXIDE_DETECTOR => [
                self::CO,
                self::CO_DETECTED,
            ],

            Capability::VOC_MEASUREMENT => [
                self::VOC,
            ],

            Capability::PM25_MEASUREMENT => [
                self::PM25,
                self::PM10,
            ],

            Capability::ILLUMINANCE_MEASUREMENT => [
                self::ILLUMINANCE,
            ],

            Capability::UV_INDEX => [
                self::UV_INDEX,
            ],

            Capability::NOISE_LEVEL => [
                self::NOISE_LEVEL,
            ],

            Capability::MOTION_SENSOR => [
                self::MOTION,
                self::MOTION_DETECTED,
                self::LAST_MOTION,
            ],

            Capability::OCCUPANCY_SENSOR => [
                self::OCCUPANCY,
                self::OCCUPANCY_DETECTED,
                self::LAST_OCCUPIED,
            ],

            Capability::CONTACT_SENSOR => [
                self::CONTACT,
                self::CONTACT_OPEN,
                self::LAST_OPENED,
                self::LAST_CLOSED,
            ],

            Capability::VIBRATION_SENSOR => [
                self::VIBRATION,
                self::VIBRATION_DETECTED,
            ],

            Capability::WATER_LEAK_DETECTOR => [
                self::WATER_LEAK,
                self::WATER_LEAK_DETECTED,
            ],

            Capability::SMOKE_DETECTOR => [
                self::SMOKE,
                self::SMOKE_DETECTED,
            ],

            Capability::GLASS_BREAK_DETECTOR => [
                self::GLASS_BREAK,
                self::GLASS_BREAK_DETECTED,
            ],

            Capability::TAMPER_ALERT => [
                self::TAMPER,
                self::TAMPER_DETECTED,
            ],

            Capability::LOCK => [
                self::LOCK_STATE,
                self::IS_LOCKED,
            ],

            Capability::DOOR_CONTROL => [
                self::DOOR_STATE,
                self::IS_DOOR_OPEN,
            ],

            Capability::ALARM,
            Capability::SECURITY_SYSTEM => [
                self::ALARM_STATE,
                self::IS_ARMED,
                self::ALARM_TRIGGERED,
                self::INTRUSION_DETECTED,
                self::LAST_INTRUSION,
            ],

            Capability::CAMERA => [
                self::CAMERA_STATE,
                self::IS_STREAMING,
                self::IS_RECORDING,
                self::STREAM_URL,
                self::SNAPSHOT_URL,
                self::PAN_POSITION,
                self::TILT_POSITION,
                self::ZOOM_LEVEL,
                self::PRESET,
                self::MOTION_DETECTION_ENABLED,
                self::NIGHT_VISION_ENABLED,
                self::AUDIO_ENABLED,
                self::PRIVACY_MODE,
                self::RECORDING_DURATION,
                self::LAST_RECORDING,
            ],

            Capability::THERMOSTAT_MODE => [
                self::THERMOSTAT_MODE,
                self::HVAC_MODE,
                self::THERMOSTAT_OPERATING_STATE,
                self::IS_HEATING,
                self::IS_COOLING,
            ],

            Capability::THERMOSTAT_HEATING_SETPOINT => [
                self::HEATING_SETPOINT,
                self::TARGET_TEMPERATURE,
            ],

            Capability::THERMOSTAT_COOLING_SETPOINT => [
                self::COOLING_SETPOINT,
            ],

            Capability::THERMOSTAT_FAN_MODE, Capability::FAN_MODE => [
                self::FAN_MODE,
                self::FAN_STATE,
            ],

            Capability::THERMOSTAT_OPERATING_STATE => [
                self::THERMOSTAT_OPERATING_STATE,
                self::IS_HEATING,
                self::IS_COOLING,
                self::CURRENT_TEMPERATURE,
            ],

            Capability::FAN_SPEED => [
                self::FAN_SPEED,
                self::FAN_SPEED_PERCENT,
            ],

            Capability::FAN_OSCILLATION => [
                self::OSCILLATION,
                self::IS_OSCILLATING,
            ],

            Capability::HUMIDIFIER,
            Capability::DEHUMIDIFIER => [
                self::STATE,
                self::HUMIDIFIER_STATE,
                self::DEHUMIDIFIER_STATE,
            ],

            Capability::HUMIDITY_CONTROL => [
                self::HUMIDITY_SETPOINT,
                self::TARGET_HUMIDITY,
                self::CURRENT_HUMIDITY,
            ],

            Capability::AIR_PURIFIER => [
                self::PURIFIER_STATE,
            ],

            Capability::AIR_PURIFIER_MODE => [
                self::PURIFIER_MODE,
            ],

            Capability::FILTER_STATUS => [
                self::FILTER_LIFE_REMAINING,
            ],

            Capability::WINDOW_COVERING => [
                self::WINDOW_COVERING_STATE,
                self::IS_COVER_OPEN,
                self::IS_COVER_CLOSED,
            ],

            Capability::WINDOW_COVERING_POSITION => [
                self::POSITION,
            ],

            Capability::WINDOW_COVERING_TILT => [
                self::TILT,
                self::TILT_ANGLE,
            ],

            Capability::VALVE => [
                self::VALVE_STATE,
                self::IS_VALVE_OPEN,
                self::VALVE_POSITION,
                self::FLOW_RATE,
            ],

            Capability::AUDIO_VOLUME => [
                self::VOLUME,
                self::VOLUME_LEVEL,
            ],

            Capability::AUDIO_MUTE => [
                self::IS_MUTED,
                self::MUTE_STATE,
            ],

            Capability::MEDIA_PLAYBACK => [
                self::PLAYBACK_STATE,
                self::IS_PLAYING,
            ],

            Capability::MEDIA_TRACK_CONTROL => [
                self::MEDIA_POSITION,
                self::MEDIA_DURATION,
                self::MEDIA_PROGRESS,
            ],

            Capability::MEDIA_REPEAT => [
                self::REPEAT_MODE,
                self::IS_REPEAT,
            ],

            Capability::MEDIA_SHUFFLE => [
                self::SHUFFLE_MODE,
                self::IS_SHUFFLE,
            ],

            Capability::MEDIA_SEEK => [
                self::MEDIA_POSITION,
            ],

            Capability::INPUT_SOURCE => [
                self::INPUT_SOURCE,
            ],

            Capability::TV_CHANNEL => [
                self::CHANNEL,
                self::CHANNEL_NAME,
            ],

            Capability::SPEAKER => [
                self::STATE,
                self::VOLUME,
                self::IS_MUTED,
            ],

            Capability::MEDIA_CONTENT => [
                self::MEDIA_TITLE,
                self::MEDIA_ARTIST,
                self::MEDIA_ALBUM,
            ],

            Capability::MEDIA_IMAGE => [
                self::MEDIA_IMAGE_URL,
            ],

            Capability::BUTTON => [
                self::BUTTON_STATE,
                self::BUTTON_EVENT,
                self::LAST_BUTTON_EVENT,
            ],

            Capability::MULTI_BUTTON => [
                self::BUTTON_NUMBER,
                self::BUTTON_STATE,
                self::BUTTON_EVENT,
                self::LAST_BUTTON_EVENT,
            ],

            Capability::ROTARY_ENCODER => [
                self::ROTATION,
                self::ROTATION_ANGLE,
            ],

            Capability::INDICATOR => [
                self::INDICATOR_STATE,
                self::INDICATOR_COLOR,
            ],

            Capability::BATTERY => [
                self::BATTERY,
                self::BATTERY_PERCENTAGE,
                self::BATTERY_LOW,
                self::BATTERY_CHARGING,
            ],

            Capability::BATTERY_VOLTAGE => [
                self::BATTERY_VOLTAGE,
            ],

            Capability::SIGNAL_STRENGTH => [
                self::SIGNAL_STRENGTH,
                self::RSSI,
            ],

            Capability::LINK_QUALITY => [
                self::LINK_QUALITY,
            ],

            Capability::NETWORK_STATUS => [
                self::NETWORK_STATUS,
            ],

            Capability::REACHABLE => [
                self::IS_REACHABLE,
                self::ONLINE,
                self::LAST_SEEN,
            ],

            Capability::VACUUM_CONTROL => [
                self::VACUUM_STATE,
                self::IS_CLEANING,
                self::IS_DOCKED,
                self::CLEANED_AREA,
                self::CLEANING_TIME,
                self::ERROR_CODE,
            ],

            Capability::VACUUM_FAN_SPEED => [
                self::VACUUM_FAN_SPEED,
            ],

            Capability::VACUUM_ZONE => [
                self::CURRENT_ZONE,
            ],

            Capability::SPRINKLER,
            Capability::IRRIGATION_CONTROL => [
                self::SPRINKLER_STATE,
                self::IS_WATERING,
                self::WATERING_DURATION,
                self::REMAINING_TIME,
                self::WATER_FLOW,
                self::TOTAL_WATER_USED,
            ],

            Capability::SOIL_MOISTURE => [
                self::SOIL_MOISTURE,
                self::SOIL_TEMPERATURE,
            ],

            Capability::CURRENT_TIME => [
                self::CURRENT_TIME,
                self::CURRENT_DATETIME,
            ],

            Capability::CURRENT_DATE => [
                self::CURRENT_DATE,
                self::CURRENT_DATETIME,
            ],

            Capability::CURRENT_DAY_OF_WEEK => [
                self::DAY_OF_WEEK,
                self::WEEK_NUMBER,
            ],

            Capability::SUNRISE_TIME => [
                self::SUNRISE_TIME,
            ],

            Capability::SUNSET_TIME => [
                self::SUNSET_TIME,
            ],

            Capability::IS_DAY => [
                self::IS_DAY,
                self::IS_NIGHT,
                self::DAYLIGHT_DURATION,
            ],

            Capability::COUNTER => [
                self::COUNTER_VALUE,
                self::COUNTER_MAX,
            ],

            Capability::TIMER => [
                self::TIMER_STATE,
                self::TIMER_REMAINING,
                self::TIMER_ELAPSED,
                self::TIMER_DURATION,
            ],

            Capability::WEBHOOK => [
                self::LAST_WEBHOOK_TRIGGER,
                self::WEBHOOK_RESPONSE,
            ],

            Capability::HTTP_REQUEST => [
                self::HTTP_STATUS_CODE,
                self::HTTP_RESPONSE,
            ],

            Capability::SCRIPT_EXECUTION => [
                self::SCRIPT_OUTPUT,
            ],

            Capability::CONFIGURATION => [],

            Capability::UPDATE => [
                self::FIRMWARE_VERSION,
                self::UPDATE_AVAILABLE,
                self::UPDATE_PROGRESS,
            ],

            Capability::IDENTIFY => [],

            Capability::HEALTH_CHECK => [
                self::HEALTH_STATUS,
                self::LAST_ERROR,
            ],

            Capability::DIAGNOSTICS => [
                self::UPTIME,
                self::DEVICE_TEMPERATURE,
                self::MEMORY_USAGE,
            ],

            Capability::TIME_SYNC => [
                self::CURRENT_DATETIME,
            ],

            Capability::THERMOSTAT => array_merge(
                self::getStatesForCapability(Capability::THERMOSTAT_MODE),
                self::getStatesForCapability(Capability::THERMOSTAT_HEATING_SETPOINT),
                self::getStatesForCapability(Capability::THERMOSTAT_COOLING_SETPOINT),
                self::getStatesForCapability(Capability::THERMOSTAT_FAN_MODE),
                self::getStatesForCapability(Capability::THERMOSTAT_OPERATING_STATE),
            ),

            Capability::RGBW_LIGHT => array_merge(
                self::getStatesForCapability(Capability::LIGHT),
                self::getStatesForCapability(Capability::BRIGHTNESS),
                self::getStatesForCapability(Capability::COLOR_CONTROL),
                self::getStatesForCapability(Capability::COLOR_TEMPERATURE),
                self::getStatesForCapability(Capability::LIGHT_EFFECT),
            ),

            Capability::SMART_PLUG => array_merge(
                self::getStatesForCapability(Capability::SWITCH),
                self::getStatesForCapability(Capability::POWER_METER),
                self::getStatesForCapability(Capability::ENERGY_METER),
            ),

            Capability::SMART_LOCK => array_merge(
                self::getStatesForCapability(Capability::LOCK),
                self::getStatesForCapability(Capability::BATTERY),
            ),

            Capability::SMART_FAN => array_merge(
                self::getStatesForCapability(Capability::SWITCH),
                self::getStatesForCapability(Capability::FAN_SPEED),
                self::getStatesForCapability(Capability::FAN_OSCILLATION),
            ),

            Capability::MOTORIZED_BLIND => array_merge(
                self::getStatesForCapability(Capability::WINDOW_COVERING),
                self::getStatesForCapability(Capability::WINDOW_COVERING_POSITION),
                self::getStatesForCapability(Capability::WINDOW_COVERING_TILT),
            ),

            default => [],
        };
    }

    public function getConstraints(): CapabilityStateConstraints
    {
        return match ($this) {
            self::STATE,
            self::FAN_STATE,
            self::OSCILLATION,
            self::HUMIDIFIER_STATE,
            self::DEHUMIDIFIER_STATE,
            self::PURIFIER_STATE,
            self::SHUFFLE_MODE,
            self::INDICATOR_STATE => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::STRING,
                allowedValues: ['on', 'off'],
            ),

            self::BRIGHTNESS,
            self::SATURATION,
            self::BATTERY_PERCENTAGE,
            self::BATTERY,
            self::FAN_SPEED_PERCENT,
            self::FILTER_LIFE_REMAINING,
            self::POSITION,
            self::TILT,
            self::VALVE_POSITION,
            self::VOLUME,
            self::VOLUME_LEVEL,
            self::MEDIA_PROGRESS,
            self::VACUUM_BATTERY,
            self::UPDATE_PROGRESS,
            self::MEMORY_USAGE => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::INTEGER,
                min: 0,
                max: 100,
                unit: '%',
            ),

            self::COLOR_TEMPERATURE => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::INTEGER,
                min: 1000,  // Kelvin minimum (blanc très chaud)
                max: 10000, // Kelvin maximum (blanc très froid)
                unit: 'K',
            ),

            self::COLOR_TEMPERATURE_MIRED => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::INTEGER,
                min: 100,   // Mired minimum
                max: 1000,  // Mired maximum
            ),

            self::COLOR_MODE => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::STRING,
                allowedValues: ['xy', 'hs', 'temp', 'rgb'],
            ),

            self::HUE => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::INTEGER,
                min: 0,
                max: 360,
                unit: '°',
            ),

            self::EFFECT => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::STRING,
                allowedValues: ['none', 'blink', 'breathe', 'colorloop', 'okay', 'finish_effect'],
            ),

            self::TRANSITION => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::INTEGER,
                min: 0,
                max: 65535, // Millisecondes
                unit: 'ms',
            ),

            self::POWER => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::FLOAT,
                min: 0,
                max: 100000, // 100kW max
                unit: 'W',
                precision: 2,
            ),

            self::ENERGY => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::FLOAT,
                min: 0,
                max: null, // Pas de max (cumulatif)
                unit: 'kWh',
                precision: 3,
            ),

            self::VOLTAGE => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::FLOAT,
                min: 0,
                max: 500, // 500V max
                unit: 'V',
                precision: 1,
            ),

            self::CURRENT => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::FLOAT,
                min: 0,
                max: 100, // 100A max
                unit: 'A',
                precision: 2,
            ),

            self::POWER_FACTOR => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::FLOAT,
                min: 0,
                max: 1,
                precision: 3,
            ),

            self::BATTERY_VOLTAGE => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::FLOAT,
                min: 0,
                max: 50, // 50V max (batteries)
                unit: 'V',
                precision: 2,
            ),

            self::POWER_SOURCE => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::STRING,
                allowedValues: ['mains', 'battery', 'dc', 'emergency', 'unknown'],
            ),

            self::TEMPERATURE => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::FLOAT,
                min: -50,  // -50°C min
                max: 100,  // 100°C max
                unit: '°C',
                precision: 1,
            ),

            self::HUMIDITY,
            self::HUMIDITY_SETPOINT,
            self::TARGET_HUMIDITY,
            self::CURRENT_HUMIDITY,
            self::SOIL_MOISTURE => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::FLOAT,
                min: 0,
                max: 100,
                unit: '%',
                precision: 1,
            ),

            self::PRESSURE => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::FLOAT,
                min: 500,  // hPa minimum (tempête extrême)
                max: 1100, // hPa maximum
                unit: 'hPa',
                precision: 1,
            ),

            self::AIR_QUALITY => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::STRING,
                allowedValues: ['excellent', 'good', 'moderate', 'poor', 'unhealthy', 'hazardous'],
            ),

            self::AIR_QUALITY_INDEX => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::INTEGER,
                min: 0,
                max: 500,
            ),

            self::CO2 => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::INTEGER,
                min: 0,
                max: 10000, // ppm
                unit: 'ppm',
            ),

            self::CO2_LEVEL => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::STRING,
                allowedValues: ['normal', 'warning', 'critical'],
            ),

            self::CO => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::INTEGER,
                min: 0,
                max: 1000, // ppm
                unit: 'ppm',
            ),

            self::CO_DETECTED,
            self::MOTION_DETECTED,
            self::OCCUPANCY_DETECTED,
            self::CONTACT_OPEN,
            self::VIBRATION_DETECTED,
            self::WATER_LEAK_DETECTED,
            self::SMOKE_DETECTED,
            self::GLASS_BREAK_DETECTED,
            self::TAMPER_DETECTED,
            self::IS_LOCKED,
            self::IS_DOOR_OPEN,
            self::IS_ARMED,
            self::ALARM_TRIGGERED,
            self::INTRUSION_DETECTED,
            self::IS_STREAMING,
            self::IS_RECORDING,
            self::MOTION_DETECTION_ENABLED,
            self::NIGHT_VISION_ENABLED,
            self::AUDIO_ENABLED,
            self::PRIVACY_MODE,
            self::IS_HEATING,
            self::IS_COOLING,
            self::IS_OSCILLATING,
            self::IS_COVER_OPEN,
            self::IS_COVER_CLOSED,
            self::IS_VALVE_OPEN,
            self::IS_MUTED,
            self::IS_PLAYING,
            self::IS_REPEAT,
            self::IS_SHUFFLE,
            self::IS_CLEANING,
            self::IS_DOCKED,
            self::IS_WATERING,
            self::IS_DAY,
            self::IS_NIGHT,
            self::ONLINE,
            self::UPDATE_AVAILABLE,
            self::BATTERY_LOW,
            self::BATTERY_CHARGING,
            self::IS_REACHABLE => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::BOOLEAN,
            ),

            self::VOC => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::INTEGER,
                min: 0,
                max: 60000, // ppb
                unit: 'ppb',
            ),

            self::PM25 => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::INTEGER,
                min: 0,
                max: 1000, // µg/m³
                unit: 'µg/m³',
            ),

            self::PM10 => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::INTEGER,
                min: 0,
                max: 2000, // µg/m³
                unit: 'µg/m³',
            ),

            self::ILLUMINANCE => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::INTEGER,
                min: 0,
                max: 100000, // lux
                unit: 'lux',
            ),

            self::UV_INDEX => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::INTEGER,
                min: 0,
                max: 15,
            ),

            self::NOISE_LEVEL => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::INTEGER,
                min: 0,
                max: 140, // dB
                unit: 'dB',
            ),

            self::MOTION,
            self::VIBRATION,
            self::WATER_LEAK,
            self::SMOKE,
            self::GLASS_BREAK,
            self::TAMPER => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::STRING,
                allowedValues: ['detected', 'clear'],
            ),

            self::OCCUPANCY => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::STRING,
                allowedValues: ['occupied', 'unoccupied'],
            ),

            self::CONTACT => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::STRING,
                allowedValues: ['open', 'closed'],
            ),

            self::LOCK_STATE => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::STRING,
                allowedValues: ['locked', 'unlocked', 'jammed', 'unknown'],
            ),

            self::DOOR_STATE,
            self::WINDOW_COVERING_STATE => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::STRING,
                allowedValues: ['open', 'closed', 'opening', 'closing', 'stopped', 'unknown'],
            ),

            self::ALARM_STATE => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::STRING,
                allowedValues: ['disarmed', 'armed_away', 'armed_home', 'armed_night', 'triggered'],
            ),

            self::SECURITY_SYSTEM_STATE => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::STRING,
                allowedValues: ['armed', 'disarmed', 'triggered', 'arming', 'disarming'],
            ),

            self::CAMERA_STATE => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::STRING,
                allowedValues: ['streaming', 'recording', 'idle', 'unavailable'],
            ),

            self::PAN_POSITION => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::INTEGER,
                min: -180,
                max: 180,
                unit: '°',
            ),

            self::TILT_POSITION,
            self::TILT_ANGLE => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::INTEGER,
                min: -90,
                max: 90,
                unit: '°',
            ),

            self::ZOOM_LEVEL => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::INTEGER,
                min: 1,
                max: 10,
            ),

            self::THERMOSTAT_MODE,
            self::HVAC_MODE => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::STRING,
                allowedValues: ['off', 'heat', 'cool', 'auto', 'dry', 'fan_only', 'emergency_heat'],
            ),

            self::HEATING_SETPOINT,
            self::COOLING_SETPOINT,
            self::TARGET_TEMPERATURE,
            self::CURRENT_TEMPERATURE => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::FLOAT,
                min: 5,   // 5°C min
                max: 35,  // 35°C max
                unit: '°C',
                precision: 1,
            ),

            self::THERMOSTAT_OPERATING_STATE => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::STRING,
                allowedValues: ['idle', 'heating', 'cooling', 'fan_only', 'pending_heat', 'pending_cool'],
            ),

            self::FAN_MODE => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::STRING,
                allowedValues: ['auto', 'on', 'off', 'low', 'medium', 'high', 'circulate'],
            ),

            self::FAN_SPEED => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::STRING,
                allowedValues: ['off', 'low', 'medium', 'high', 'auto'],
            ),

            self::PURIFIER_MODE => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::STRING,
                allowedValues: ['auto', 'sleep', 'low', 'medium', 'high', 'turbo'],
            ),

            self::VALVE_STATE => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::STRING,
                allowedValues: ['open', 'closed', 'opening', 'closing'],
            ),

            self::FLOW_RATE,
            self::WATER_FLOW => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::FLOAT,
                min: 0,
                max: 1000, // L/min
                unit: 'L/min',
                precision: 2,
            ),

            self::MUTE_STATE => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::STRING,
                allowedValues: ['muted', 'unmuted'],
            ),

            self::PLAYBACK_STATE => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::STRING,
                allowedValues: ['playing', 'paused', 'stopped', 'buffering'],
            ),

            self::MEDIA_POSITION,
            self::MEDIA_DURATION => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::INTEGER,
                min: 0,
                max: null, // Pas de max
                unit: 's',
            ),

            self::REPEAT_MODE => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::STRING,
                allowedValues: ['off', 'one', 'all'],
            ),

            self::BUTTON_STATE => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::STRING,
                allowedValues: ['single', 'double', 'triple', 'long', 'release', 'hold'],
            ),

            self::ROTATION => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::STRING,
                allowedValues: ['left', 'right'],
            ),

            self::ROTATION_ANGLE => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::INTEGER,
                min: -360,
                max: 360,
                unit: '°',
            ),

            self::SIGNAL_STRENGTH,
            self::RSSI => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::INTEGER,
                min: -120, // dBm
                max: 0,
                unit: 'dBm',
            ),

            self::LINK_QUALITY => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::INTEGER,
                min: 0,
                max: 255,
            ),

            self::NETWORK_STATUS => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::STRING,
                allowedValues: ['online', 'offline', 'connecting'],
            ),

            self::VACUUM_STATE => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::STRING,
                allowedValues: ['cleaning', 'paused', 'returning', 'docked', 'idle', 'error'],
            ),

            self::VACUUM_FAN_SPEED => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::STRING,
                allowedValues: ['silent', 'low', 'medium', 'high', 'max', 'turbo'],
            ),

            self::CLEANED_AREA => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::FLOAT,
                min: 0,
                max: null,
                unit: 'm²',
                precision: 1,
            ),

            self::CLEANING_TIME => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::INTEGER,
                min: 0,
                max: null,
                unit: 'min',
            ),

            self::SPRINKLER_STATE => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::STRING,
                allowedValues: ['watering', 'idle', 'scheduled'],
            ),

            self::WATERING_DURATION,
            self::REMAINING_TIME => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::INTEGER,
                min: 0,
                max: 1440, // 24h max
                unit: 'min',
            ),

            self::SOIL_TEMPERATURE => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::FLOAT,
                min: -10,
                max: 50,
                unit: '°C',
                precision: 1,
            ),

            self::CURRENT_TIME => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::STRING,
                pattern: '/^\d{2}:\d{2}:\d{2}$/', // HH:mm:ss
            ),

            self::CURRENT_DATE => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::STRING,
                pattern: '/^\d{4}-\d{2}-\d{2}$/', // YYYY-MM-DD
            ),

            self::DAY_OF_WEEK => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::STRING,
                allowedValues: ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'],
            ),

            self::WEEK_NUMBER => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::INTEGER,
                min: 1,
                max: 53,
            ),

            self::COUNTER_VALUE => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::INTEGER,
                min: 0,
                max: null,
            ),

            self::TIMER_STATE => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::STRING,
                allowedValues: ['running', 'stopped', 'paused'],
            ),

            self::TIMER_REMAINING,
            self::TIMER_ELAPSED,
            self::TIMER_DURATION, self::UPTIME => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::INTEGER,
                min: 0,
                max: null,
                unit: 's',
            ),

            self::HEALTH_STATUS => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::STRING,
                allowedValues: ['healthy', 'warning', 'error', 'critical'],
            ),

            self::DEVICE_TEMPERATURE => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::FLOAT,
                min: 0,
                max: 100,
                unit: '°C',
                precision: 1,
            ),

            self::LAST_MOTION,
            self::LAST_OCCUPIED,
            self::LAST_OPENED,
            self::LAST_CLOSED,
            self::LAST_INTRUSION,
            self::LAST_RECORDING,
            self::LAST_BUTTON_EVENT,
            self::LAST_WEBHOOK_TRIGGER,
            self::LAST_SEEN,
            self::CURRENT_DATETIME => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::DATETIME,
            ),

            self::MEDIA_TITLE,
            self::MEDIA_ARTIST,
            self::MEDIA_ALBUM,
            self::CHANNEL_NAME,
            self::PRESET,
            self::CURRENT_ZONE,
            self::ERROR_CODE,
            self::FIRMWARE_VERSION,
            self::LAST_ERROR,
            self::STREAM_URL,
            self::SNAPSHOT_URL,
            self::MEDIA_IMAGE_URL,
            self::HTTP_RESPONSE,
            self::WEBHOOK_RESPONSE,
            self::SCRIPT_OUTPUT,
            self::INPUT_SOURCE,
            self::INDICATOR_COLOR => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::STRING,
            ),

            self::COLOR_RGB, self::COLOR_HSV, self::COLOR_XY => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::OBJECT,
            ),

            self::CHANNEL,
            self::BUTTON_NUMBER,
            self::COUNTER_MAX,
            self::HTTP_STATUS_CODE,
            self::RECORDING_DURATION,
            self::FILTER_REMAINING,
            self::BRUSH_REMAINING,
            self::SIDE_BRUSH_REMAINING,
            self::TOTAL_WATER_USED,
            self::DAYLIGHT_DURATION,
            self::APPARENT_POWER,
            self::REACTIVE_POWER => new CapabilityStateConstraints(
                dataType: CapabilityStateDataType::INTEGER,
                min: 0,
            ),

            default => new CapabilityStateConstraints(
                dataType: $this->getDataType(),
            ),
        };
    }
}
