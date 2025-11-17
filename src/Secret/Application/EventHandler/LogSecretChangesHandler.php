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

use Marvin\Secret\Domain\Event\SecretCreated;
use Marvin\Secret\Domain\Event\SecretDeleted;
use Marvin\Secret\Domain\Event\SecretRotated;
use Marvin\Secret\Domain\Event\SecretUpdated;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class LogSecretChangesHandler
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(
        SecretCreated|SecretUpdated|SecretRotated|SecretDeleted $event
    ): void {
        $context = [
            'secret_id' => $event->secretId,
            'key' => $event->key,
            'category' => $event->category,
            'occurred_on' => $event->occurredOn->format('c'),
        ];

        match (true) {
            $event instanceof SecretCreated => $this->logger->info(
                'Secret created',
                $context
            ),
            $event instanceof SecretUpdated => $this->logger->info(
                'Secret updated',
                array_merge($context, ['value_changed' => $event->valueChanged])
            ),
            $event instanceof SecretRotated => $this->logger->info(
                'Secret rotated',
                array_merge($context, ['automatic' => $event->automatic])
            ),
            $event instanceof SecretDeleted => $this->logger->warning(
                'Secret deleted',
                $context
            ),
        };
    }
}
