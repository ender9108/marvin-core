<?php

namespace Marvin\Secret\Application\CommandHandler;

use Marvin\Secret\Application\Command\RotateSecret;
use Marvin\Secret\Application\Command\RotateSecrets;
use Marvin\Secret\Application\Service\PasswordGeneratorInterface;
use Marvin\Secret\Application\Service\SecretRotationService;
use Marvin\Secret\Domain\Exception\AutoGenerateError;
use Marvin\Secret\Domain\Exception\SecretNotFound;
use Marvin\Secret\Domain\Repository\SecretRepositoryInterface;
use Marvin\Secret\Domain\Service\EncryptionServiceInterface;
use Marvin\Secret\Domain\ValueObject\SecretValue;
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
