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

use Exception;
use Marvin\Secret\Application\Service\SecretRotationService;
use Marvin\Shared\Presentation\Exception\Service\ExceptionMessageManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:secrets:rotate',
    description: 'Rotate secrets that need rotation',
    help: <<<HELP
Automatically rotate managed secrets based on their rotation policy.

Examples:
  php bin/console marvin:secrets:rotate
  php bin/console marvin:secrets:rotate --dry-run
HELP
)]
final readonly class RotateSecretsCommand
{
    public function __construct(
        private SecretRotationService $rotationService,
        private ExceptionMessageManager $exceptionMessageManager,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Option(description: 'Show which secrets would be rotated', name: 'dry-run')]
        bool $dryRun = false,
    ): int {
        try {
            $io->title('Secret Rotation');

            if ($dryRun) {
                $secrets = $this->rotationService->findSecretsNeedingRotation();

                if (empty($secrets)) {
                    $io->success('No secrets need rotation');
                    return Command::SUCCESS;
                }

                $io->section('Secrets that would be rotated:');
                $rows = [];
                foreach ($secrets as $secret) {
                    $rows[] = [
                        $secret->key->value,
                        $secret->category->value,
                        $secret->lastRotatedAt?->format('Y-m-d H:i'),
                        $secret->rotationPolicy->getRotationIntervalDays() . ' days',
                    ];
                }
                $io->table(['Key', 'Category', 'Last Rotated', 'Interval'], $rows);

                return Command::SUCCESS;
            }

            $rotated = $this->rotationService->rotateExpiredSecrets();

            if (empty($rotated)) {
                $io->success('No secrets needed rotation');
            } else {
                $io->success(sprintf(
                    'Rotated %d secret(s): %s',
                    count($rotated),
                    implode(', ', $rotated)
                ));
            }

            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error($this->exceptionMessageManager->cliResponseFormat($e));
            return Command::FAILURE;
        }
    }
}
