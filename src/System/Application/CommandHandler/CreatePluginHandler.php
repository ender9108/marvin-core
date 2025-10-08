<?php

namespace Marvin\System\Application\CommandHandler;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use Marvin\System\Application\Command\CreatePlugin;
use Marvin\System\Domain\Exception\PluginAlreadyExist;
use Marvin\System\Domain\Model\Plugin;
use Marvin\System\Domain\Repository\PluginRepositoryInterface;
use Marvin\System\Domain\ValueObject\PluginStatus;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CreatePluginHandler implements SyncCommandHandlerInterface
{
    public function __construct(
        private PluginRepositoryInterface $pluginRepository,
    ) {
    }

    public function __invoke(CreatePlugin $message): Plugin
    {
        if ($this->pluginRepository->exists($message->reference)) {
            throw PluginAlreadyExist::withReference($message->reference);
        }

        $plugin = new Plugin(
            $message->label,
            $message->reference,
            $message->version,
            PluginStatus::enabled(),
            $message->metadata,
            $message->description,
        );

        $this->pluginRepository->save($plugin);

        return $plugin;
    }
}
