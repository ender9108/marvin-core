<?php

namespace EnderLab\DddCqrsBundle\Infrastructure\Doctrine;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use EnderLab\DddCqrsBundle\Domain\Aggregate\AggregateRoot;
use EnderLab\DddCqrsBundle\Domain\Event\Bus\DomainEventBus;
use EnderLab\DddCqrsBundle\Domain\Event\DomainEventInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;

#[AsDoctrineListener(event: Events::onFlush)]
#[AsDoctrineListener(event: Events::postFlush)]
final class DomainEventDispatcher
{
    /** @var DomainEventInterface[] */
    private array $eventsToDispatch = [];

    public function __construct(private readonly DomainEventBus $eventBus)
    {
    }

    public function onFlush(OnFlushEventArgs $args): void
    {
        $uow = $args->getObjectManager()->getUnitOfWork();

        // On capture insertions, updates et deletions
        $entities = array_merge(
            $uow->getScheduledEntityInsertions(),
            $uow->getScheduledEntityUpdates(),
            $uow->getScheduledEntityDeletions()
        );

        foreach ($entities as $entity) {
            if (!$entity instanceof AggregateRoot) {
                continue;
            }

            foreach ($entity->releaseEvents() as $event) {
                $this->eventsToDispatch[] = $event;
            }
        }
    }

    /**
     * @throws ExceptionInterface
     */
    public function postFlush(PostFlushEventArgs $args): void
    {
        if (empty($this->eventsToDispatch)) {
            return;
        }

        foreach ($this->eventsToDispatch as $event) {
            $this->eventBus->publish($event);
        }

        $this->eventsToDispatch = [];
    }
}
