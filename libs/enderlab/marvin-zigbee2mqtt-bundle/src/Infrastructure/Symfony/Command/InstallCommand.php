<?php

namespace EnderLab\Zigbee2mqttBundle\Infrastructure\Symfony\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\Exception\ExceptionInterface;

/**
 * Installation
 *
 * registerPlugin:
 *      label => Zigbee2mqtt
 *      reference => zigbee2mqtt
 *      version => x.x.x
 *      description => Zigbee2mqtt plugin for manage zigbee devices
 *      status => enbaled
 *
 *  registerProtocol:
 *       label => Zigbee2mqtt
 *       reference => zigbee2mqtt
 *       description => Zigbee2mqtt protocol for manage zigbee devices
 *       status => enbaled
 *
 *
 * registerDocker
 * registerSupervisorWorker
 * registerExchange
 *
 */
#[AsCommand(
    name: 'marvin:zigbee2mqtt:install',
    description: 'Install zigbee2mqtt bundle',
)]
class InstallCommand extends AbstractZigbeePluginManagerCommand
{
    /**
     * @throws ExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->info('Start zigbee2mqtt installation');

        $this->startInstall(function() {
            $this->registerPlugin(
                'Zigbee2mqtt',
                true,
                'Zigbee2mqtt plugin for manage zigbee devices',
            );
            $this->registerProtocol(
                'Zigbee2Mqtt',
                'protocol_zigbee2mqtt',
                true,
                'Zigbee2mqtt protocol for manager zigbee devices',
            );
            /*$this->registerDocker(
                'config/docker/compose.zigbee.yaml',
                ['config/docker/config'],
                []
            );
            $this->registerSupervisorWorker(realpath('config/supervisor'));*/
        }, $input, $output);

        $io->success('Zigbee2mqtt is now installed');
        return Command::SUCCESS;
    }
}
