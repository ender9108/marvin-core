<?php

namespace EnderLab\MqttBundle\Command;

use App\System\Infrastructure\Symfony\Command\AbstractPluginManagerCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Exception;
use Symfony\Component\Messenger\Exception\ExceptionInterface;

#[AsCommand(
    name: 'marvin:mqtt:install',
    description: 'Install mqtt bundle',
)]
class MqttInstallCommand extends AbstractPluginManagerCommand
{
    /**
     * @throws Exception|ExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $rootPath = __DIR__.'/../../';
        $reference = $this->parameters->get('plugin_reference');
        $version = $this->getBundleVersion($rootPath.'composer.json');
        $io = new SymfonyStyle($input, $output);
        $io->info('Start mqtt installation');

        $this->startInstall(function() use ($version, $rootPath) {
            $this->checkPluginRequirement($this->parameters->get('plugin_requirements'));
            $this->registerPlugin($this->reference, [], $version);
            $this->registerSupervisor(realpath($rootPath.'config/supervisor'));
        }, $reference, $input, $output);

        $io->success('Mqtt is now installed');

        return Command::SUCCESS;
    }
}
