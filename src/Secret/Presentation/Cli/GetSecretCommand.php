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
use Marvin\Secret\Application\Query\GetSecret;
use Marvin\Secret\Domain\Model\Secret;
use Marvin\Secret\Domain\ValueObject\SecretKey;
use Marvin\Shared\Presentation\Exception\Service\ExceptionMessageManager;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:secret:get',
    description: 'Get a secret by key',
)]
final readonly class GetSecretCommand
{
    public function __construct(
        private QueryBusInterface $queryBus,
        private ExceptionMessageManager $exceptionMessageManager,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument(description: 'Secret key', name: 'key')]
        string $key,
        #[Option(description: 'Show decrypted value', name: 'decrypt')]
        bool $decrypt = false,
    ): int {
        try {
            /** @var Secret $secret */
            $secret = $this->queryBus->handle(new GetSecret(
                key: SecretKey::fromString($key),
                decrypted: $decrypt,
            ));

            $data = [
                ['ID', $secret->id->toString()],
                ['Key', $secret->key->value],
                ['Scope', $secret->scope->value],
                ['Category', $secret->category->value],
            ];

            if ($decrypt) {
                $io->warning('⚠️  Decrypted value shown - handle with care!');
                $data[] = ['Value (decrypted)', $secret->value->getDecrypted()];
            } else {
                $data[] = ['Value (encrypted)', substr($secret->value->getEncrypted(), 0, 50) . '...'];
            }

            $data[] = ['Auto Rotate', $secret->rotationPolicy?->isAutoRotate() ? '✓' : '✗'];
            $data[] = ['Rotation Interval', $secret->rotationPolicy?->getRotationIntervalDays() . ' days'];
            $data[] = ['Last Rotated', $secret->lastRotatedAt?->format('Y-m-d H:i:s') ?? 'Never'];
            $data[] = ['Expires At', $secret->expiresAt?->format('Y-m-d H:i:s') ?? 'Never'];
            $data[] = ['Is Expired', $secret->isExpired() ? '✓' : '✗'];
            $data[] = ['Created At', $secret->createdAt->format('Y-m-d H:i:s')];

            $io->definitionList(...$data);

            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error($this->exceptionMessageManager->cliResponseFormat($e));

            return Command::FAILURE;
        }
    }
}
