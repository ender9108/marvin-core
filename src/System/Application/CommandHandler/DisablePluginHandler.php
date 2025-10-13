<?php

namespace Marvin\System\Application\CommandHandler;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use Marvin\System\Application\Command\DisablePlugin;
use Marvin\System\Domain\Model\Plugin;
use Marvin\System\Domain\Repository\PluginRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class DisablePluginHandler implements SyncCommandHandlerInterface
{
    public function __construct(
        private PluginRepositoryInterface $pluginRepository,
    ) {
    }

    public function __invoke(DisablePlugin $message): Plugin
    {
        $plugin = $this->pluginRepository->byId($message->id);
        $plugin->disable();
        $this->pluginRepository->save($plugin);

        return $plugin;
    }
}
