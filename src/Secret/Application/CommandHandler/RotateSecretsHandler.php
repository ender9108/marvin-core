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

namespace Marvin\Secret\Application\CommandHandler;

use Marvin\Secret\Application\Command\RotateSecrets;
use Marvin\Secret\Application\Service\SecretRotationService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class RotateSecretsHandler
{
    public function __construct(
        private SecretRotationService $secretRotationService,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(RotateSecrets $command): void
    {
        $secrets = $this->secretRotationService->rotateExpiredSecrets();

        $this->logger->info('Secret rotated successfully', [
            'secret_key' => implode(', ', $secrets),
        ]);
    }
}
