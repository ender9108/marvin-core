<?php

/**
 * Marvin Core - DDD-based home automation system
 *
 * @package   Marvin\Core
 * @author    Alexandre Berthelot <alexandreberthelot9108@gmail.com>
 * @copyright 2024-present Alexandre Berthelot
 * @license   AGPL-3.0 License
 * @link      https://github.com/ender9108/marvin-core
 */

declare(strict_types=1);

namespace Marvin\Shared\Infrastructure\Persistence\Doctrine\ORM\Listener;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

//#[AsDoctrineListener(event: Events::preUpdate)]
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

            $this->propertyAccessor->setValue(
                $entity,
                'updatedAt',
                $value
            );
        }
    }
}
