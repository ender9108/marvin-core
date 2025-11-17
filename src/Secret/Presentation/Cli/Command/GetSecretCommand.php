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

namespace Marvin\Secret\Presentation\Cli\Command;

use EnderLab\DddCqrsBundle\Application\Query\QueryBusInterface;
use Exception;
use Marvin\Secret\Application\Query\GetSecret;
use Marvin\Secret\Domain\Model\Secret;
use Marvin\Secret\Domain\Service\EncryptionServiceInterface;
use Marvin\Secret\Domain\ValueObject\SecretKey;
use Marvin\Shared\Presentation\Exception\Service\ExceptionMessageManager;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:secrets:get',
    description: 'Retrieve a secret value',
    help: <<<HELP
Retrieve a secret value.

Examples:
  php bin/console marvin:secrets:get device_http_123_api_key
  php bin/console marvin:secrets:get wifi_password --show-metadata
HELP
)]
final readonly class GetSecretCommand
{
    public function __construct(
        private QueryBusInterface $queryBus,
        private ExceptionMessageManager $exceptionMessageManager,
        private EncryptionServiceInterface $encryption,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument(description: 'Secret key (e.g., device_http_abc123_api_key)', name: 'key')]
        string $key,
        #[Option(description: 'Show metadata', name: 'show-metadata')]
        bool $showMetadata = false,
    ): int {
        try {
            /** @var Secret $secret */
            $secret = $this->queryBus->handle(
                new GetSecret(new SecretKey($key), true)
            );

            $io->section("Secret: {$key}");

            $plainValue = $secret->value->decrypt($this->encryption);
            $io->writeln("<info>{$plainValue}</info>");

            if ($showMetadata) {
                $io->newLine();
                $io->table(
                    ['Property', 'Value'],
                    [
                        ['Category', $secret->category->value],
                        ['Scope', $secret->scope->value],
                        ['Management', $secret->rotationPolicy->getManagement()->value],
                        ['Auto-rotate', $secret->rotationPolicy->isAutoRotate() ? 'Yes' : 'No'],
                        ['Rotation interval', $secret->rotationPolicy->getRotationIntervalDays() . ' days'],
                        ['Last rotated', $secret->lastRotatedAt?->format('Y-m-d H:i:s') ?? 'Never'],
                        ['Expires at', $secret->expiresAt?->format('Y-m-d H:i:s') ?? 'Never'],
                        ['Created at', $secret->createdAt->format('Y-m-d H:i:s')],
                    ]
                );
            }

            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error($this->exceptionMessageManager->cliResponseFormat($e));
            return Command::FAILURE;
        }
    }
}
