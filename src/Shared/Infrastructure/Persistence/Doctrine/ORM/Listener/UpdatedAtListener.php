<?php

namespace Marvin\Shared\Infrastructure\Persistence\Doctrine\ORM\Listener;

use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

#[AsDoctrineListener(event: Events::preUpdate)]
final readonly class UpdatedAtListener
{
    public function __construct(
        private PropertyAccessorInterface $propertyAccessor,
    ) {
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        if (property_exists($entity, 'updatedAt')) {
            $this->propertyAccessor->setValue(
                $entity,
                'updatedAt',
                new DateTimeImmutable()
            );
        }
    }
}
