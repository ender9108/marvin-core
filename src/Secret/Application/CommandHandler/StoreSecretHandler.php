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

use Marvin\Secret\Application\Command\StoreSecret;
use Marvin\Secret\Domain\Exception\SecretAlreadyExists;
use Marvin\Secret\Domain\Model\Secret;
use Marvin\Secret\Domain\Repository\SecretRepositoryInterface;
use Marvin\Secret\Domain\Service\EncryptionServiceInterface;
use Marvin\Secret\Domain\ValueObject\RotationPolicy;
use Marvin\Secret\Domain\ValueObject\SecretValue;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class StoreSecretHandler
{
    public function __construct(
        private SecretRepositoryInterface $secretRepository,
        private EncryptionServiceInterface $encryption,
    ) {
    }

    public function __invoke(StoreSecret $command): void
    {
        // Vérifier si le secret existe déjà
        if ($this->secretRepository->exists($command->key)) {
            throw SecretAlreadyExists::withKey($command->key);
        }

        $value = SecretValue::fromPlainText($command->plainTextValue, $this->encryption);

        $rotationPolicy = $command->managed
            ? ($command->autoRotate
                ? RotationPolicy::managed($command->rotationIntervalDays, $command->rotationCommand)
                : RotationPolicy::managedNoRotation())
            : RotationPolicy::external($command->rotationIntervalDays);

        $secret = Secret::create(
            key: $command->key,
            value: $value,
            scope: $command->scope,
            category: $command->category,
            rotationPolicy: $rotationPolicy,
            expiresAt: $command->expiresAt,
            metadata: $command->metadata,
        );

        $this->secretRepository->save($secret);
    }
}
