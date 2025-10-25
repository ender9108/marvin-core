<?php

namespace Marvin\Secret\Application\Service;

use Marvin\Secret\Domain\Model\Secret;
use Marvin\Secret\Domain\Repository\SecretRepositoryInterface;
use Marvin\Secret\Domain\Service\EncryptionServiceInterface;
use Marvin\Secret\Domain\ValueObject\SecretValue;
use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Process;

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
     * Tourne tous les secrets qui nécessitent une rotation
     *
     * @return string[] Liste des clés tournées
     */
    public function rotateExpiredSecrets(): array
    {
        $secrets = $this->secretRepository->getNeedingRotation();
        $rotated = [];

        foreach ($secrets as $secret) {
            try {
                $this->rotateSecret($secret);
                $rotated[] = $secret->getKey()->toString();

                $this->logger->info('Secret rotated successfully', [
                    'secret_key' => $secret->getKey()->toString(),
                ]);
            } catch (\Exception $e) {
                $this->logger->error('Failed to rotate secret', [
                    'secret_key' => $secret->getKey()->toString(),
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $rotated;
    }

    /**
     * Tourne un secret spécifique
     */
    public function rotateSecret(Secret $secret): void
    {
        // 1. Générer nouvelle valeur
        $newPassword = $this->passwordGenerator->generate();
        $newValue = SecretValue::fromPlainText($newPassword, $this->encryption);

        // 2. Mettre à jour le secret
        $secret->rotate($newValue);
        $this->secretRepository->save($secret);

        // 3. Exécuter commande post-rotation si définie
        if ($command = $secret->rotationPolicy->getRotationCommand()) {
            $this->executeRotationCommand($command, $secret);
        }
    }

    /**
     * @return Secret[]
     */
    public function findSecretsNeedingRotation(): array
    {
        return $this->secretRepository->getNeedingRotation();
    }

    private function executeRotationCommand(string $command, Secret $secret): void
    {
        $this->logger->info('Executing rotation command', [
            'command' => $command,
            'secret_key' => $secret->key->value,
        ]);

        $process = Process::fromShellCommandline($command);
        $process->setTimeout(300); // 5 minutes max
        $process->run();

        if (!$process->isSuccessful()) {
            /** @todo */
            throw new \RuntimeException(
                sprintf('Rotation command failed: %s', $process->getErrorOutput())
            );
        }
    }
}
