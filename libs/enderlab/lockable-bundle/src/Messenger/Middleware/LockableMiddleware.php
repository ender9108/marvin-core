<?php

namespace EnderLab\LockableBundle\Messenger\Middleware;

use EnderLab\LockableBundle\Attribute\AsLockableMessage;
use Psr\Cache\InvalidArgumentException;
use ReflectionClass;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Lock\Exception\ExceptionInterface;
use Symfony\Component\Lock\Exception\LockConflictedException;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\RecoverableMessageHandlingException;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Throwable;

class LockableMiddleware implements MiddlewareInterface
{
    public const int CACHE_TIMEOUT = 600;

    private array $locks = [];

    public function __construct(
        private readonly LockFactory $lockFactory,
        private readonly CacheInterface $cache,
        private readonly ParameterBagInterface $parameters,
    ) {
    }

    /**
     * @throws Throwable
     * @throws InvalidArgumentException
     */
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $message = $envelope->getMessage();
        $lockConfig = $this->getLockConfig($message);

        if (null === $lockConfig) {
            return $stack->next()->handle($envelope, $stack);
        }

        try {
            $this->refreshLocks();
            $this->acquireLock($lockConfig);

            try {
                return $stack->next()->handle($envelope, $stack);
            } finally {
                $this->releaseLocks();
            }
        } catch (ExceptionInterface $e) {
            throw new RecoverableMessageHandlingException('Failed to acquire lock', 0, $e);
        }
    }

    private function acquireLock(AsLockableMessage $lockableMessage): void
    {
        if (isset($this->locks[$lockableMessage->getLockName()])) {
            return;
        }

        $lock = $this->lockFactory->createLock($lockableMessage->getLockName(), $lockableMessage->getTtl());

        if ($lockableMessage->isRead()) {
            $res = $lock->acquireRead($lockableMessage->isBlocking());
        } else {
            $res = $lock->acquire($lockableMessage->isBlocking());
        }

        if (!$res) {
            $this->releaseLocks();
            throw new LockConflictedException(sprintf('Failed to acquire the lock for "%s".', $lockableMessage->getLockName()));
        }

        $this->locks[$lockableMessage->getLockName()] = $lock;
    }

    /**
     * @throws InvalidArgumentException
     */
    private function getLockConfig(object $message): ?object
    {
        $cacheKey = md5(self::class.'_'.get_class($message));

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($message): object {
            $item->expiresAfter($this->parameters->get('lockable_cache_timeout'));

            $reflectionClass = new ReflectionClass($message);
            $reflectionAttributes = $reflectionClass->getAttributes(AsLockableMessage::class);
            $lockableAttribute = null;

            foreach ($reflectionAttributes as $reflectionAttribute) {
                $lockableAttribute = $reflectionAttribute->newInstance();
                break;
            }

            return $lockableAttribute;
        });
    }

    private function releaseLocks(): void
    {
        foreach ($this->locks as $lock) {
            try {
                $lock->release();
            } catch (Throwable $e) {
            }
        }

        $this->locks = [];
    }

    /**
     * @throws Throwable
     */
    private function refreshLocks(): void
    {
        foreach ($this->locks as $lock) {
            try {
                $lock->refresh();
            } catch (Throwable $e) {
                $this->releaseLocks();
                throw $e;
            }
        }
    }

    private function getLockNameEvaluated(AsLockableMessage $lockableMessage): string
    {

    }
}
