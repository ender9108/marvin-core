<?php

namespace EnderLab\MarvinPluginZigbee2mqttBundle\Domotic\Infrastructure\Framework\Symfony\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;

#[AsCommand(
    name: 'marvin:zigbee2mqtt:mqtt-listener',
    description: 'Worker MQTT listener',
)]
class WorkerMqttListenerCommand
{
    public function __invoke(): int {
        return Command::SUCCESS;
    }
}
