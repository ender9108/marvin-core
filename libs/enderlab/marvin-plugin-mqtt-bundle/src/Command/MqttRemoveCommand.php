<?php

namespace EnderLab\MqttBundle\Command;

use App\System\Infrastructure\Symfony\Command\AbstractPluginManagerCommand;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'marvin:mqtt:remove',
    description: 'Remove mqtt bundle',
)]
class MqttRemoveCommand extends AbstractPluginManagerCommand
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
