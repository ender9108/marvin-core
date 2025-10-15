<?php

namespace Marvin\Shared\Infrastructure\Persistence\Doctrine\ORM\Listener;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Marvin\Shared\Domain\ValueObject\CreatedAt;
use Marvin\Shared\Domain\ValueObject\UpdatedAt;
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
            $value = null;

            if ($entity->updatedAt instanceof DateTimeInterface) {
                $value = new DateTimeImmutable();
            }

            if ($entity->updatedAt instanceof CreatedAt) {
                $value = new UpdatedAt();
            }

            $this->propertyAccessor->setValue(
                $entity,
                'updatedAt',
                $value
            );
        }
    }
}
