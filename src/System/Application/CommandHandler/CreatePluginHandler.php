<?php

namespace Marvin\System\Application\CommandHandler;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use Marvin\System\Application\Command\CreatePlugin;
use Marvin\System\Domain\Exception\PluginAlreadyExist;
use Marvin\System\Domain\Exception\PluginStatusNotFound;
use Marvin\System\Domain\Model\Plugin;
use Marvin\System\Domain\Model\PluginStatus;
use Marvin\System\Domain\Repository\PluginRepositoryInterface;
use Marvin\System\Domain\Repository\PluginStatusRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CreatePluginHandler implements SyncCommandHandlerInterface
{
    public function __construct(
        private PluginRepositoryInterface $pluginRepository,
        private PluginStatusRepositoryInterface $pluginStatusRepository,
    ) {
    }

    public function __invoke(CreatePlugin $message): Plugin
    {
        if ($this->pluginRepository->exists($message->reference)) {
            throw PluginAlreadyExist::withReference($message->reference);
        }

        /** @var PluginStatus $pluginStatus */
        $pluginStatus = $this->pluginStatusRepository->findOneBy([
            'reference' => $message->statusReference->value,
        ]);

        if (null === $pluginStatus) {
            throw PluginStatusNotFound::withReference($message->statusReference);
        }

        $plugin = new Plugin(
            $message->label,
            $message->reference,
            $message->version,
            $pluginStatus,
            $message->metadata,
            $message->description,
        );

        $this->pluginRepository->save($plugin);

        return $plugin;
    }
}
