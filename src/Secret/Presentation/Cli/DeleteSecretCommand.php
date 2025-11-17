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

namespace Marvin\Secret\Presentation\Cli;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use Exception;
use Marvin\Secret\Application\Command\DeleteSecret;
use Marvin\Secret\Domain\ValueObject\SecretKey;
use Marvin\Shared\Presentation\Exception\Service\ExceptionMessageManager;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:secret:delete',
    description: 'Delete a secret',
)]
final readonly class DeleteSecretCommand
{
    public function __construct(
        private SyncCommandBusInterface $syncCommandBus,
        private ExceptionMessageManager $exceptionMessageManager,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument(description: 'Secret key', name: 'key')]
        string $key,
    ): int {
        try {
            if (!$io->confirm(sprintf('Are you sure you want to delete the secret "%s"?', $key), false)) {
                $io->info('Operation cancelled.');
                return Command::SUCCESS;
            }

            $this->syncCommandBus->handle(new DeleteSecret(
                key: SecretKey::fromString($key),
            ));

            $io->success(sprintf('Secret "%s" deleted successfully.', $key));

            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error($this->exceptionMessageManager->cliResponseFormat($e));

            return Command::FAILURE;
        }
    }
}
