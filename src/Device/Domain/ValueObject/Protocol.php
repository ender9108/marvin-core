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
 * Protocol - Type de protocol logique du device (vue métier)
 *
 * Différent de Protocol Context TransportType (vue technique)
 *
 * Mapping Protocol → TransportType :
 * - ZIGBEE → MQTT (via Zigbee2MQTT bridge)
 * - MQTT → MQTT (natif)
 * - NETWORK → REST ou JSONRPC (selon device, ex: Shelly, caméras IP)
 * - BLUETOOTH → MQTT (via ESP32 Proxy)
 * - MATTER → WEBSOCKET (Thread Border Router)
 * - THREAD → WEBSOCKET (Border Router)
 * - ZWAVE → REST ou MQTT (via controller)
 */
enum Protocol: string
{
    use EnumToArrayTrait;
    use ValueObjectTrait;

    /**
     * ZIGBEE - Devices Zigbee via Zigbee2MQTT
     * Exemples : Philips Hue, IKEA Tradfri, Xiaomi Aqara
     */
    case ZIGBEE = 'zigbee';

    /**
     * MQTT - Devices MQTT natifs
     * Exemples : Tasmota, ESPHome, Shelly en mode MQTT
     */
    case MQTT = 'mqtt';

    /**
     * NETWORK - Devices réseau avec API REST/JSON-RPC/WebSocket
     * Exemples : Shelly Gen1 (REST), Shelly Gen2 (JSON-RPC), caméras IP, thermostats
     */
    case NETWORK = 'network';

    /**
     * BLUETOOTH - Devices Bluetooth via proxy ESP32
     * Exemples : Capteurs Xiaomi Mi, Ruuvi Tag, serrures BLE
     */
    case BLUETOOTH = 'bluetooth';

    /**
     * MATTER - Standard unificateur (futur)
     */
    case MATTER = 'matter';

    /**
     * THREAD - Réseau maillé IP (futur)
     */
    case THREAD = 'thread';

    /**
     * ZWAVE - Alternative à Zigbee (futur)
     */
    case ZWAVE = 'zwave';
}
