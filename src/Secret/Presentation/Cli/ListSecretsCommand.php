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

use EnderLab\DddCqrsBundle\Application\Query\QueryBusInterface;
use Exception;
use Marvin\Secret\Application\Query\GetSecretCollection;
use Marvin\Secret\Domain\Model\Secret;
use Marvin\Shared\Presentation\Exception\Service\ExceptionMessageManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:secret:list',
    description: 'List all secrets',
)]
final readonly class ListSecretsCommand
{
    public function __construct(
        private QueryBusInterface $queryBus,
        private ExceptionMessageManager $exceptionMessageManager,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Option(description: 'Filter by scope', name: 'scope')]
        ?string $scope = null,
        #[Option(description: 'Filter by category', name: 'category')]
        ?string $category = null,
        #[Option(description: 'Show only expired secrets', name: 'expired')]
        bool $showExpired = false,
    ): int {
        try {
            $filters = [];
            if (null !== $scope) {
                $filters['scope'] = $scope;
            }
            if (null !== $category) {
                $filters['category'] = $category;
            }

            /** @var array<Secret> $secrets */
            $secrets = $this->queryBus->handle(new GetSecretCollection(filters: $filters));

            if (empty($secrets)) {
                $io->info('No secrets found.');
                return Command::SUCCESS;
            }

            $rows = [];
            foreach ($secrets as $secret) {
                // Skip if filtering expired and not expired
                if ($showExpired && !$secret->isExpired()) {
                    continue;
                }

                $rows[] = [
                    $secret->id->toString(),
                    $secret->key->value,
                    $secret->scope->value,
                    $secret->category->value,
                    $secret->rotationPolicy?->isAutoRotate() ? '✓' : '✗',
                    $secret->rotationPolicy?->getRotationIntervalDays() ?? 'N/A',
                    $secret->lastRotatedAt?->format('Y-m-d H:i:s') ?? 'Never',
                    $secret->expiresAt?->format('Y-m-d') ?? 'Never',
                    $secret->isExpired() ? '⚠️  Yes' : 'No',
                    $secret->createdAt->format('Y-m-d H:i:s'),
                ];
            }

            if (empty($rows)) {
                $io->info('No secrets matching the criteria.');
                return Command::SUCCESS;
            }

            $io->table(
                ['ID', 'Key', 'Scope', 'Category', 'Auto Rotate', 'Interval (days)', 'Last Rotated', 'Expires', 'Expired', 'Created'],
                $rows
            );

            $io->success(sprintf('Found %d secret(s).', count($rows)));

            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error($this->exceptionMessageManager->cliResponseFormat($e));

            return Command::FAILURE;
        }
    }
}
