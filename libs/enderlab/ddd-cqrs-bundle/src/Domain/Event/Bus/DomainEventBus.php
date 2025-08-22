<?php

namespace EnderLab\DddCqrsBundle\Domain\Event\Bus;

use EnderLab\DddCqrsBundle\Domain\Event\DomainEventInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class DomainEventBus
{
    public function __construct(
        #[Autowire(service: 'domain_event.bus')]
        private MessageBusInterface $eventBus,
    ) {}

    /**
     * @throws ExceptionInterface
     */
    public function publish(DomainEventInterface $event): void
    {
        $this->eventBus->dispatch($event);
    }
}
