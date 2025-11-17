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

use DateTimeImmutable;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use Exception;
use Marvin\Secret\Application\Command\StoreSecret;
use Marvin\Secret\Domain\ValueObject\SecretCategory;
use Marvin\Secret\Domain\ValueObject\SecretKey;
use Marvin\Secret\Domain\ValueObject\SecretScope;
use Marvin\Shared\Presentation\Exception\Service\ExceptionMessageManager;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:secret:store',
    description: 'Store a new secret',
)]
final readonly class StoreSecretCommand
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
        #[Argument(description: 'Secret value', name: 'value')]
        string $value,
        #[Option(description: 'Secret scope (global, domain, service)', name: 'scope')]
        string $scope = 'global',
        #[Option(description: 'Secret category (infrastructure, api_key, database, application)', name: 'category')]
        string $category = 'infrastructure',
        #[Option(description: 'Rotation interval in days', name: 'rotation-interval')]
        int $rotationInterval = 0,
        #[Option(description: 'Enable auto rotation', name: 'auto-rotate')]
        bool $autoRotate = false,
        #[Option(description: 'Rotation command', name: 'rotation-command')]
        ?string $rotationCommand = null,
        #[Option(description: 'Expiration date (YYYY-MM-DD)', name: 'expires-at')]
        ?string $expiresAt = null,
    ): int {
        try {
            $expiresAtDate = null;
            if (null !== $expiresAt) {
                $expiresAtDate = new DateTimeImmutable($expiresAt);
            }

            $this->syncCommandBus->handle(new StoreSecret(
                key: SecretKey::fromString($key),
                plainTextValue: $value,
                scope: SecretScope::from($scope),
                category: SecretCategory::from($category),
                managed: false,
                rotationIntervalDays: $rotationInterval,
                autoRotate: $autoRotate,
                rotationCommand: $rotationCommand,
                expiresAt: $expiresAtDate,
            ));

            $io->success(sprintf('Secret "%s" stored successfully.', $key));

            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error($this->exceptionMessageManager->cliResponseFormat($e));

            return Command::FAILURE;
        }
    }
}
