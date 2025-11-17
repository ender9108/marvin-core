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
use EnderLab\DddCqrsBundle\Domain\Repository\PaginatorInterface;
use Exception;
use Marvin\Secret\Application\Query\GetSecretCollection;
use Marvin\Secret\Domain\Model\Secret;
use Marvin\Shared\Presentation\Exception\Service\ExceptionMessageManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:secrets:list',
    description: 'List all stored secrets (keys only, not values)',
)]
final readonly class ListSecretsCommand
{
    public function __construct(
        private QueryBusInterface $queryBus,
        private ExceptionMessageManager $exceptionMessageManager,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io
    ): int {
        try {
            $io->title('Stored Secrets');

            /** @var PaginatorInterface $paginator */
            $paginator = $this->queryBus->handle(new GetSecretCollection());

            if ($paginator->count() === 0) {
                $io->warning('No secrets stored yet');
                return Command::SUCCESS;
            }

            $rows = [];

            /** @var Secret $secret */
            foreach ($paginator->getIterator() as $secret) {
                $rows[] = [
                    $secret->key->value,
                    $secret->category->value,
                    $secret->scope->value,
                    $secret->rotationPolicy->getManagement()->isManaged() ? 'yes' : 'no',
                    $secret->rotationPolicy->getManagement()->canAutoRotate() ? 'yes' : 'no',
                    $secret->lastRotatedAt?->format('Y-m-d H:i:s') ?? 'Never',
                    $secret->createdAt->format('Y-m-d H:i:s')
                ];
            }

            $io->table(
                ['Key', 'Category', 'Scope', 'Management', 'Auto-rotate', 'Last Rotated', 'Created'],
                $rows
            );

            $io->note(sprintf('Total: %d secret(s)', $paginator->count()));

            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error($this->exceptionMessageManager->cliResponseFormat($e));
            return Command::FAILURE;
        }
    }
}
