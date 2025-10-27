<?php

namespace Marvin\Secret\Application\EventHandler;

use Marvin\Secret\Domain\Event\SecretExpired;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CheckSecretExpirationHandler
{
    public function __construct(
        private LoggerInterface $logger,
    ) {}

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
