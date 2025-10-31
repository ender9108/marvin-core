<?php

namespace Marvin\Secret\Application\CommandHandler;

use Marvin\Secret\Application\Command\RotateSecret;
use Marvin\Secret\Application\Service\PasswordGeneratorInterface;
use Marvin\Secret\Domain\Exception\AutoGenerateError;
use Marvin\Secret\Domain\Exception\SecretNotFound;
use Marvin\Secret\Domain\Repository\SecretRepositoryInterface;
use Marvin\Secret\Domain\Service\EncryptionServiceInterface;
use Marvin\Secret\Domain\ValueObject\SecretValue;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class RotateSecretHandler
{
    public function __construct(
        private SecretRepositoryInterface $secretRepository,
        private EncryptionServiceInterface $encryption,
        private PasswordGeneratorInterface $passwordGenerator,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(RotateSecret $command): void
    {
        $secret = $this->secretRepository->byKey($command->key);

        if (null === $secret) {
            throw SecretNotFound::withKey($command->key);
        }

        // Si external et pas de nouvelle valeur → erreur
        if ($secret->rotationPolicy->getManagement()->isExternal() && $command->newValue === null) {
            throw AutoGenerateError::withKey($command->key);
        }

        // Générer ou utiliser la nouvelle valeur
        $plainValue = $command->newValue ?? $this->passwordGenerator->generate();
        $newValue = SecretValue::fromPlainText($plainValue, $this->encryption);

        // Rotater le secret
        $secret->rotate($newValue);
        $this->secretRepository->save($secret);

        $this->logger->info('Secret rotated successfully', [
            'secret_key' => $command->key,
            'managed' => $secret->rotationPolicy->getManagement()->value,
        ]);
    }
}
