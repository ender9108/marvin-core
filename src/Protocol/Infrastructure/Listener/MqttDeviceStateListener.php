<?php

declare(strict_types=1);

namespace Marvin\Protocol\Infrastructure\Listener;

use function Swoole\Coroutine\run;
use EnderLab\DddCqrsBundle\Application\Event\DomainEventBusInterface;
use Marvin\Protocol\Domain\Model\ProtocolAdapterInterface;
use Marvin\Protocol\Infrastructure\Protocol\MqttProtocol;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

/**
 * MQTT Device State Listener
 * Listens to MQTT messages and transforms them into domain events using adapters
 */
final readonly class MqttDeviceStateListener
{
    /**
     * @param iterable<ProtocolAdapterInterface> $adapters
     */
    public function __construct(
        #[AutowireIterator(tag: 'protocol.adapter')]
        private iterable $adapters,
        private MqttProtocol $mqttProtocol,
        private DomainEventBusInterface $eventBus,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * Start listening to MQTT messages
     *
     * @param array<string> $topics Topics to subscribe to
     * @param int|null $timeout Timeout in seconds (null for infinite)
     */
    public function listen(array $topics = ['#'], ?int $timeout = null): void
    {
        $this->logger->info('Starting MQTT Device State Listener', [
            'topics' => $topics,
            'timeout' => $timeout,
        ]);

        run(function () use ($topics, $timeout): void {
            // Connect to MQTT broker
            $this->mqttProtocol->connect();

            // Subscribe to topics
            $this->mqttProtocol->subscribe($topics);

            // Receive and process messages
            $this->mqttProtocol->receive(function (string $topic, array|string $payload, array $properties): void {
                $this->handleMessage($topic, $payload, $properties);
            }, $timeout);
        });
    }

    /**
     * Handle incoming MQTT message
     */
    private function handleMessage(string $topic, array|string $payload, array $properties): void
    {
        /*$this->logger->debug('Received MQTT message', [
            'topic' => $topic,
            'payload' => $payload,
            'properties' => $properties,
        ]);*/

        dump(
            $topic,
            $payload,
            $properties,
        );

        //php bin/console protocol:mqtt:listen --topics=zigbee2mqtt2marvin/#

        // Ensure payload is array
        /*if (!is_array($payload)) {
            $this->logger->warning('Payload is not an array, skipping', [
                'topic' => $topic,
                'payload' => $payload,
            ]);
            return;
        }

        // Try to find an adapter that can handle this message
        foreach ($this->adapters as $adapter) {
            try {
                $transformedData = $adapter->transformMessage($topic, $payload);

                if ($transformedData !== null) {
                    $this->logger->info('Message transformed by adapter', [
                        'adapter' => $adapter->getName(),
                        'topic' => $topic,
                        'data' => $transformedData,
                    ]);

                    // Dispatch DeviceStateChanged event to Device Context
                    // transformedData contains: nativeId, capabilities, metadata
                    $states = [];
                    foreach ($transformedData['capabilities'] ?? [] as $capability) {
                        $states[$capability['name']] = $capability['value'];

                        // Add unit if present
                        if (isset($capability['unit'])) {
                            $states[$capability['name'] . '_unit'] = $capability['unit'];
                        }
                    }

                    $event = new DeviceStateChanged(
                        deviceId: $transformedData['nativeId'],
                        states: $states
                    );

                    $this->eventBus->dispatch($event);

                    $this->logger->info('DeviceStateChanged event dispatched', [
                        'deviceId' => $transformedData['nativeId'],
                        'capabilitiesCount' => count($states),
                    ]);

                    // Only use the first adapter that successfully transforms the message
                    break;
                }
            } catch (Exception $e) {
                $this->logger->error('Error transforming message with adapter', [
                    'adapter' => $adapter->getName(),
                    'topic' => $topic,
                    'error' => $e->getMessage(),
                ]);
            }
        }*/
    }

    public function __destruct()
    {
        $this->mqttProtocol->disconnect();
    }
}
