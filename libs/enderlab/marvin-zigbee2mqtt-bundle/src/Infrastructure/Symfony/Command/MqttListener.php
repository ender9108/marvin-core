<?php

namespace EnderLab\Zigbee2mqttBundle\Infrastructure\Symfony\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;

#[AsCommand(
    name: 'marvin:zigbee2mqtt:mqtt_listeneer',
    description: 'Listen MQTT messages',
)]
class MqttListener extends Command
{
    public function __invoke(

    ): int {
        return Command::SUCCESS;
    }
}
