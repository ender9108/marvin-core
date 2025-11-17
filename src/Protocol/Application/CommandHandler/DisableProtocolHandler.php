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

use Marvin\Protocol\Application\Command\DisableProtocol;
use Marvin\Protocol\Domain\Repository\ProtocolRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class DisableProtocolHandler
{
    public function __construct(
        private ProtocolRepositoryInterface $protocolRepository,
    ) {
    }

    public function __invoke(DisableProtocol $command): void
    {
        $protocol = $this->protocolRepository->byId($command->protocolId);

        $protocol->disconnect();

        $this->protocolRepository->save($protocol);
    }
}
