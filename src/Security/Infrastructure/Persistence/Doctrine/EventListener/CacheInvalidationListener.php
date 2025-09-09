<?php
namespace Marvin\Security\Infrastructure\Persistence\Doctrine\EventListener;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Infrastructure\Persistence\Doctrine\Cache\UserCacheKeys;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;

#[AsDoctrineListener(Events::postRemove)]
#[AsDoctrineListener(Events::postUpdate)]
#[AsDoctrineListener(Events::postPersist)]
final readonly class CacheInvalidationListener
{
    private ?CacheItemPoolInterface $resultCache;

    public function __construct(
        private Connection $connection,
        private LoggerInterface $logger,
    ) {
        $this->resultCache = $this->connection->getConfiguration()->getResultCache();
    }

    /**
     * @throws InvalidArgumentException
     */
    public function __invoke(LifecycleEventArgs $event): void
    {
        $entity = $event->getObject();
        $cacheKeys = [];

        switch (true) {
            case $entity instanceof User:
                $cacheKeys = [
                    UserCacheKeys::USER_ITEM->withId($entity->id),
                    UserCacheKeys::USER_LIST->value,
                ];
                break;
            default:
                $this->logger->info('No cache keys found for entity: ' . get_class($entity));
                break;
        }

        if (!empty($cacheKeys)) {
            $this->resultCache?->deleteItems($cacheKeys);
        }
    }
}
