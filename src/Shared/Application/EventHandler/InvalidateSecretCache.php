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
