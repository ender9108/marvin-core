<?php

namespace EnderLab\Zigbee2mqttBundle\Infrastructure\Symfony\Command;

use App\System\Infrastructure\Symfony\Command\AbstractPluginManagerCommand;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'marvin:zigbee2mqtt:remove',
    description: 'Remove zigbee2mqtt bundle',
)]
class Zigbee2MqttRemoveCommand extends AbstractPluginManagerCommand
{
    protected function configure(): void
    {
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return Command::SUCCESS;
    }


}
