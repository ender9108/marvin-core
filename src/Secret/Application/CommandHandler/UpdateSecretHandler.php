<?php

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
