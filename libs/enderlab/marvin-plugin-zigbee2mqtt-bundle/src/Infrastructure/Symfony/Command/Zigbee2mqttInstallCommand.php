<?php

namespace EnderLab\Zigbee2mqttBundle\Infrastructure\Symfony\Command;

use App\System\Infrastructure\Symfony\Command\AbstractPluginManagerCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\Exception\ExceptionInterface;

#[AsCommand(
    name: 'marvin:zigbee2mqtt:install',
    description: 'Install zigbee2mqtt bundle',
)]
class Zigbee2mqttInstallCommand extends AbstractPluginManagerCommand
{
    /**
     * @throws ExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->info('Start zigbee2mqtt installation');

        $this->startInstall(function() {
            $this->checkPluginRequirement();
            $this->registerPlugin('Zigbee2mqtt');
            $this->registerProtocol('Zigbee2Mqtt', 'protocol_zigbee2mqtt');
            /*$this->registerContainer(
                'config/docker/compose.zigbee.yaml',
                ['config/docker/config'],
                []
            );
            $this->registerSupervisor(realpath('config/supervisor'));*/
        }, $input, $output);

        $io->success('Zigbee2mqtt is now installed');
        return Command::SUCCESS;
    }


    protected function getPluginRootPath(): string
    {
        return __DIR__.'/../../../../';
    }

    protected function getPluginReference(): string
    {
        return $this->parameters->get('plugin_reference');
    }

    protected function getPluginRequirements(): array
    {
        return $this->parameters->get('plugin_requirements');
    }
}
