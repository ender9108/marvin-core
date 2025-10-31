<?php

namespace Marvin\Secret\Application\Service;

use Exception;
use Marvin\Secret\Domain\Exception\AutoGenerateError;
use Marvin\Secret\Domain\Model\Secret;
use Marvin\Secret\Domain\Repository\SecretRepositoryInterface;
use Marvin\Secret\Domain\Service\EncryptionServiceInterface;
use Marvin\Secret\Domain\ValueObject\SecretValue;
use Psr\Log\LoggerInterface;

final readonly class SecretRotationService
{
    public function __construct(
        private SecretRepositoryInterface $secretRepository,
        private EncryptionServiceInterface $encryption,
        private PasswordGeneratorInterface $passwordGenerator,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @return string[] Liste des clés tournées
     */
    public function rotateExpiredSecrets(): array
    {
        $secrets = $this->secretRepository->getNeedingRotation();
        $rotated = [];

        /** @var Secret $secret */
        foreach ($secrets as $secret) {
            try {
                $this->rotateSecret($secret);
                $rotated[] = $secret->key->value;

                $this->logger->info('Secret rotated successfully', [
                    'secret_key' => $secret->key->value,
                ]);
            } catch (Exception $e) {
                $this->logger->error('Failed to rotate secret', [
                    'secret_key' => $secret->key->value,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $rotated;
    }

    public function rotateSecret(Secret $secret): void
    {
        if (!$secret->rotationPolicy->getManagement()->isManaged()) {
            throw AutoGenerateError::withKey($secret->key);
        }

        // Générer nouvelle valeur
        $newPassword = $this->passwordGenerator->generate();
        $newValue = SecretValue::fromPlainText($newPassword, $this->encryption);

        // Mettre à jour le secret
        $secret->rotate($newValue);
        $this->secretRepository->save($secret);
    }

    /**
     * @return Secret[]
     */
    public function findSecretsNeedingRotation(): array
    {
        return $this->secretRepository->getNeedingRotation();
    }
}
