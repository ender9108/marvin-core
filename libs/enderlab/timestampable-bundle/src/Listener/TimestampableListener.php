<?php

namespace EnderLab\TimestampableBundle\Listener;

use EnderLab\TimestampableBundle\Interface\TimestampableInterface;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::prePersist)]
#[AsDoctrineListener(event: Events::preUpdate)]
readonly class TimestampableListener
{
    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof TimestampableInterface && null === $entity->getCreatedAt()) {
            $entity->setCreatedAt(new DateTimeImmutable());
        }
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();
        $changes = $args->getEntityChangeSet();

        if ($entity instanceof TimestampableInterface && !isset($changes['updatedAt'])) {
            $entity->setUpdatedAt(new DateTimeImmutable());
        }
    }
}
