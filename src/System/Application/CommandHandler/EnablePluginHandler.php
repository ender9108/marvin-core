<?php

namespace Marvin\System\Application\CommandHandler;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use Marvin\System\Application\Command\EnablePlugin;
use Marvin\System\Domain\Model\Plugin;
use Marvin\System\Domain\Repository\PluginRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class EnablePluginHandler implements SyncCommandHandlerInterface
{
    public function __construct(
        private PluginRepositoryInterface $pluginRepository,
    ) {
    }

    public function __invoke(EnablePlugin $message): Plugin
    {
        $plugin = $this->pluginRepository->byId($message->id);
        $plugin->enable();
        $this->pluginRepository->save($plugin);

        return $plugin;
    }
}
