<?php

namespace EnderLab\Zigbee2mqttBundle\Infrastructure\Symfony\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\Exception\ExceptionInterface;

/**
 * Remove
 *
 */
#[AsCommand(
    name: 'marvin:zigbee2mqtt:remove',
    description: 'Remove zigbee2mqtt bundle',
)]
class RemoveCommand extends AbstractZigbeePluginManagerCommand
{
    /**
     * @throws ExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->info('Start zigbee2mqtt remove');

        $this->startUpdate(function() {
            /** @todo */
        }, $input, $output);

        $io->success('Zigbee2mqtt is now removed');
        return Command::SUCCESS;
    }
}
