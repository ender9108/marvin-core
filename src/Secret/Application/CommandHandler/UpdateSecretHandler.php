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

use Marvin\Secret\Application\Command\UpdateSecret;
use Marvin\Secret\Domain\Repository\SecretRepositoryInterface;
use Marvin\Secret\Domain\Service\EncryptionServiceInterface;
use Marvin\Secret\Domain\ValueObject\SecretValue;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class UpdateSecretHandler
{
    public function __construct(
        private SecretRepositoryInterface $secretRepository,
        private EncryptionServiceInterface $encryption,
    ) {
    }

    public function __invoke(UpdateSecret $command): void
    {
        $secret = $this->secretRepository->byKey($command->key);

        $newValue = SecretValue::fromPlainText($command->newValue, $this->encryption);
        $secret->updateValue($newValue);

        $this->secretRepository->save($secret);
    }
}
