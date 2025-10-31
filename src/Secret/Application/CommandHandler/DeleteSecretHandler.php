<?php

namespace Marvin\Secret\Application\CommandHandler;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use Marvin\Secret\Application\Command\DeleteSecret;
use Marvin\Secret\Domain\Exception\SecretNotFound;
use Marvin\Secret\Domain\Repository\SecretRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class DeleteSecretHandler implements SyncCommandHandlerInterface
{
    public function __construct(
        private SecretRepositoryInterface $secretRepository,
    ) {
    }

    public function __invoke(DeleteSecret $command): void
    {
        $secret = $this->secretRepository->byKey($command->key);

        if (null === $secret) {
            throw SecretNotFound::withKey($command->key);
        }

        $secret->delete();

        $this->secretRepository->remove($secret);
    }
}
