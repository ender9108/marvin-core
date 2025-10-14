<?php

namespace EnderLab\MarvinPluginZigbee2mqttBundle\Domotic\Infrastructure\Framework\Symfony\Command;
use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use Marvin\Domotic\Domain\Repository\ProtocolRepositoryInterface;
use Marvin\Shared\Domain\ValueObject\Reference;
use Marvin\Shared\Presentation\Exception\Service\ExceptionMessageManager;
use Marvin\System\Infrastructure\Framework\Symfony\Service\MqttClient;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(
    name: 'marvin:zigbee2mqtt:mqtt-device-discover',
    description: 'Worker MQTT device discover',
)]
final readonly class MqttDeviceDiscoverCommand
{
    public function __construct(
        private MqttClient $mqttClient,
        private ProtocolRepositoryInterface $protocolRepository,
        private ParameterBagInterface $parameters,
        private ExceptionMessageManager $exceptionMessageManager,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io
    ): int {
        try {
            $protocol = $this->protocolRepository->byReference(
                new Reference($this->parameters->get('plugin_reference'))
            );

            $this->mqttClient->connect();
            $this->mqttClient->subscribe([
                'zigbee2mqtt/bridge/devices' => 0,
                'zigbee2mqtt/bridge/event' => 0,
            ]);
            $this->mqttClient->loop(function ($topic, $payload) use ($io) {
                $topic = $packet['topic'] ?? '';
                $payload = $packet['message'] ?? '';
                $data = json_decode($payload, true);

                if (in_array(
                    $topic,
                    ['zigbee2mqtt/bridge/devices', 'zigbee2mqtt/bridge/event'],
                    true
                ) && is_array($data)) {
                    $this->handleMessage($topic, $data);
                }
            });
        } catch (DomainException $de) {
            $io->error($this->exceptionMessageManager->cliResponseFormat($de));

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function handleMessage(string $topic, array $message): void
    {

    }
}
