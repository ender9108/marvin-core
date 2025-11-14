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

namespace Marvin\Protocol\Infrastructure\Framework\Symfony\Service;

use Marvin\Protocol\Domain\Exception\AdapterNotFoundException;
use Marvin\Protocol\Domain\Model\ProtocolAdapterInterface;
use Marvin\Protocol\Domain\ValueObject\TransportType;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

/**
 * Registry pour tous les adapters protocol disponibles
 *
 * Utilise le tagged service Symfony pour auto-découverte des adapters
 */
final readonly class AdapterRegistry
{
    /** @var array<string, ProtocolAdapterInterface> */
    private array $adapters;

    /**
     * @param iterable<ProtocolAdapterInterface> $adapters Tagged iterator from Symfony
     */
    public function __construct(
        #[AutowireIterator(tag: 'protocol.adapter')]
        iterable $adapters
    ) {
        $this->adapters = $this->indexAdapters($adapters);
    }

    /**
     * Trouve l'adapter approprié basé sur le transport type et les metadata du device
     *
     * @param TransportType $transportType Type de transport (MQTT, REST, JSONRPC, WEBSOCKET)
     * @param array $deviceMetadata Metadata du device (model_id, manufacturer, etc.)
     * @return ProtocolAdapterInterface
     * @throws AdapterNotFoundException Si aucun adapter compatible trouvé
     */
    public function findAdapter(TransportType $transportType, array $deviceMetadata): ProtocolAdapterInterface
    {
        foreach ($this->adapters as $adapter) {
            if ($adapter->supports($transportType->value, $deviceMetadata)) {
                return $adapter;
            }
        }

        throw new AdapterNotFoundException(
            sprintf(
                'No adapter found for transport "%s" with metadata: %s',
                $transportType->value,
                json_encode($deviceMetadata, JSON_THROW_ON_ERROR)
            )
        );
    }

    /**
     * Suggère l'adapter pour un device en cours de création
     * Utilisé par le Device Context via ACL
     *
     * @param string $protocol Protocol logique (zigbee, mqtt, wifi, etc.)
     * @param array $deviceMetadata Metadata du device
     * @return string Nom de l'adapter suggéré
     * @throws AdapterNotFoundException Si aucun adapter compatible trouvé
     */
    public function suggestAdapter(string $protocol, array $deviceMetadata): string
    {
        // Mapping protocol logique → transport type potentiels
        $transportCandidates = $this->getTransportCandidates($protocol);

        foreach ($transportCandidates as $transportType) {
            try {
                $adapter = $this->findAdapter($transportType, $deviceMetadata);
                return $adapter->getName();
            } catch (AdapterNotFoundException) {
                continue;
            }
        }

        throw new AdapterNotFoundException(
            sprintf(
                'No adapter found for protocol "%s" with metadata: %s',
                $protocol,
                json_encode($deviceMetadata, JSON_THROW_ON_ERROR)
            )
        );
    }

    /**
     * Récupère un adapter par son nom
     *
     * @param string $name Nom de l'adapter (ex: "zigbee2mqtt", "tasmota")
     * @return ProtocolAdapterInterface
     * @throws AdapterNotFoundException Si adapter non trouvé
     */
    public function getAdapter(string $name): ProtocolAdapterInterface
    {
        if (!isset($this->adapters[$name])) {
            throw new AdapterNotFoundException(
                sprintf('Adapter "%s" not found in registry', $name)
            );
        }

        return $this->adapters[$name];
    }

    /**
     * Liste tous les adapters disponibles
     *
     * @return array<string, ProtocolAdapterInterface>
     */
    public function listAll(): array
    {
        return $this->adapters;
    }

    /**
     * Liste les adapters supportant un transport spécifique
     *
     * @param TransportType $transportType
     * @return array<ProtocolAdapterInterface>
     */
    public function listByTransport(TransportType $transportType): array
    {
        return array_filter(
            $this->adapters,
            fn (ProtocolAdapterInterface $adapter): bool =>
                in_array($transportType->value, $adapter->getSupportedProtocols(), true)
        );
    }

    /**
     * Indexe les adapters par nom
     *
     * @param iterable<ProtocolAdapterInterface> $adapters
     * @return array<string, ProtocolAdapterInterface>
     */
    private function indexAdapters(iterable $adapters): array
    {
        $indexed = [];
        foreach ($adapters as $adapter) {
            $indexed[$adapter->getName()] = $adapter;
        }
        return $indexed;
    }

    /**
     * Retourne les transport types potentiels pour un protocol logique
     *
     * @param string $protocol Protocol logique (zigbee, mqtt, wifi, bluetooth, etc.)
     * @return array<TransportType> Liste ordonnée par préférence
     */
    private function getTransportCandidates(string $protocol): array
    {
        return match (strtolower($protocol)) {
            'zigbee' => [TransportType::MQTT], // Zigbee2MQTT uniquement
            'mqtt' => [TransportType::MQTT],   // MQTT natif
            'wifi', 'network' => [             // WiFi peut être REST ou JSON-RPC
                TransportType::JSONRPC,        // Préférence Shelly Gen2 (plus moderne)
                TransportType::REST,           // Fallback Shelly Gen1
                TransportType::MQTT,           // Shelly en mode MQTT
            ],
            'bluetooth' => [TransportType::MQTT], // BLE via ESP32 Proxy → MQTT
            'matter' => [TransportType::WEBSOCKET], // Matter over Thread (futur)
            'thread' => [TransportType::WEBSOCKET], // Thread Border Router (futur)
            'zwave' => [                           // Z-Wave (futur)
                TransportType::MQTT,
                TransportType::REST,
            ],
            default => [                           // Fallback : tester tous
                TransportType::MQTT,
                TransportType::JSONRPC,
                TransportType::REST,
                TransportType::WEBSOCKET,
            ],
        };
    }
}
