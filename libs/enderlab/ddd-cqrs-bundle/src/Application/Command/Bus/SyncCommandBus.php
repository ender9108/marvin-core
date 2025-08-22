<?php

namespace EnderLab\DddCqrsBundle\Application\Command\Bus;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final readonly class SyncCommandBus
{
    public function __construct(
        #[Autowire(service: 'sync.command.bus')]
        private MessageBusInterface $syncCommandBus,
    ) {}

    /**
     * @throws ExceptionInterface
     */
    public function dispatch(SyncCommandInterface $command): mixed
    {
        $envelope = $this->syncCommandBus->dispatch($command);
        $handled = $envelope->last(HandledStamp::class);

        return $handled?->getResult();
    }
}
