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
    name: 'marvin:secrets:store',
    description: 'Store a secret securely',
    help: <<<HELP
Store a secret securely in Marvin.

Examples:
  # External API key (no auto-rotation)
  php bin/console marvin:secrets:store device_http_123_api_key "abc123" \
    --category api_key --external

  # Managed MQTT password (auto-rotation every 90 days)
  php bin/console marvin:secrets:store device_mqtt_456_password "pass123" \
    --category mqtt --managed --rotate-every 90

  # WiFi password (external, alert after 1 year)
  php bin/console marvin:secrets:store wifi_password "MyWifi123" \
    --category wifi --external --rotate-every 365
HELP
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
        #[Argument(description: 'Secret key (e.g., device_http_abc123_api_key)', name: 'key')]
        string $key,
        #[Argument(description: 'The secret value', name: 'value')]
        string $value,
        #[Option(description: 'Scope (global, user, device, protocol)', name: 'scope')]
        string $scope = 'global',
        #[Option(description: 'Category (network, api_key, certificate, infrastructure)', name: 'category')]
        string $category = 'infrastructure',
        #[Option(description: 'Secret is managed by Marvin (can be auto-rotated)', name: 'managed')]
        bool $managed = false,
        #[Option(description: 'Secret is external (provided by user, manual rotation only)', name: 'external')]
        bool $external = false,
        #[Option(description: 'Auto-rotate every X days (only for managed)', name: 'rotate-every')]
        int $rotateEvery = 0,
        #[Option(description: 'Command to execute after rotation', name: 'rotation-command')]
        ?string $rotationCommand = null,
        #[Option(description: 'Expiration date (ISO 8601)', name: 'expires')]
        ?string $expires = null,
    ): int {
        try {
            $expires = null !== $expires ? new DateTimeImmutable($expires) : null;

            // Validation : managed XOR external
            if ($managed && $external) {
                $io->error('Secret cannot be both managed and external');
                return Command::FAILURE;
            }

            if (!$managed && !$external) {
                $io->error('Secret must be either --managed or --external');
                return Command::FAILURE;
            }

            if (SecretScope::exists($scope) === false) {
                $io->error(sprintf('Invalid scope %s', $scope));
                return Command::FAILURE;
            }

            if (SecretCategory::exists($category) === false) {
                $io->error(sprintf('Invalid category %s', $category));
                return Command::FAILURE;
            }

            $this->syncCommandBus->handle(
                new StoreSecret(
                    key: new SecretKey($key),
                    plainTextValue: $value,
                    scope: SecretScope::from($scope),
                    category: SecretCategory::from($category),
                    managed: $managed,
                    rotationIntervalDays: $rotateEvery,
                    autoRotate: $managed && $rotateEvery > 0,
                    rotationCommand: $rotationCommand,
                    expiresAt: $expires,
                )
            );

            $io->success("✅ Secret '{$key}' stored successfully!");

            if ($managed && $rotateEvery > 0) {
                $io->note("Auto-rotation enabled: every {$rotateEvery} days");
            } elseif ($external && $rotateEvery > 0) {
                $io->note("Expiration warning: after {$rotateEvery} days (manual rotation required)");
            }

            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error($this->exceptionMessageManager->cliResponseFormat($e));
            return Command::FAILURE;
        }
    }
}
