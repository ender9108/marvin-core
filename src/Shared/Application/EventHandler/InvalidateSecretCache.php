<?php

namespace Marvin\Shared\Application\EventHandler;

use Marvin\Secret\Domain\Event\SecretDeleted;
use Marvin\Secret\Domain\Event\SecretRotated;
use Marvin\Secret\Domain\Event\SecretUpdated;
use Marvin\Shared\Infrastructure\Cache\CacheableSecretQueryService;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class InvalidateSecretCache
{
    public function __construct(
        private CacheableSecretQueryService $cachedSecretQuery,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function __invoke(
        SecretUpdated|SecretRotated|SecretDeleted $event
    ): void {
        $this->cachedSecretQuery->invalidate($event->key);
    }
}
