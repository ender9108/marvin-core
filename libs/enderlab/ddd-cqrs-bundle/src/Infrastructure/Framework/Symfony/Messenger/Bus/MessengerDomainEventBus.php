<?php

namespace EnderLab\DddCqrsBundle\Domain\Event\Bus;

use EnderLab\DddCqrsBundle\Application\Event\DomainEventBusInterface;
use EnderLab\DddCqrsBundle\Domain\Event\DomainEventInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class MessengerDomainEventBus implements DomainEventBusInterface
{
    public function __construct(
        private MessageBusInterface $messageBus
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function dispatch(DomainEventInterface $event): void
    {
        $this->messageBus->dispatch($event);
    }
}
