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

namespace Marvin\Secret\Application\EventHandler;

use Marvin\Secret\Domain\Event\SecretExpired;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CheckSecretExpirationHandler
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(SecretExpired $event): void
    {
        $this->logger->critical('Secret expired', [
            'secret_id' => $event->secretId,
            'key' => $event->key,
            'category' => $event->category,
            'expired_at' => $event->expiredAt->format('c'),
        ]);

        /*
         * @todo
         * When context notification exist => sendNotification
         */
    }
}
