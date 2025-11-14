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

namespace Marvin\Protocol\Application\CommandHandler;

use Marvin\Protocol\Application\Command\UpdateProtocolConfiguration;
use Marvin\Protocol\Domain\Repository\ProtocolRepositoryInterface;
use Marvin\Protocol\Domain\ValueObject\ExecutionMode;
use Marvin\Protocol\Domain\ValueObject\ProtocolConfiguration;
use Marvin\Shared\Domain\ValueObject\Identity\ProtocolId;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class UpdateProtocolConfigurationHandler
{
    public function __construct(
        private ProtocolRepositoryInterface $protocolRepository,
    ) {
    }

    public function __invoke(UpdateProtocolConfiguration $command): void
    {
        $protocol = $this->protocolRepository->byId(new ProtocolId($command->protocolId));

        $protocol->updateConfiguration(
            ProtocolConfiguration::fromArray($command->configuration)
        );

        if ($command->preferredExecutionMode !== null) {
            $protocol->updatePreferredExecutionMode(
                ExecutionMode::from($command->preferredExecutionMode)
            );
        }

        $this->protocolRepository->save($protocol);
    }
}
