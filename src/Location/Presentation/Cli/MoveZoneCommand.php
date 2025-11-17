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

namespace Marvin\Location\Presentation\Cli;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use Exception;
use Marvin\Location\Application\Command\Zone\MoveZone;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Presentation\Exception\Service\ExceptionMessageManager;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:location:move-zone',
    description: 'Move zone to a new parent zone',
)]
final readonly class MoveZoneCommand
{
    public function __construct(
        private SyncCommandBusInterface $syncCommandBus,
        private ExceptionMessageManager $exceptionMessageManager,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument(name: 'zoneId')]
        string $zoneId,
        #[Argument(name: 'newParentZoneId')]
        string $newParentZoneId,
    ): int {
        try {
            $command = new MoveZone(
                ZoneId::fromString($zoneId),
                ZoneId::fromString($newParentZoneId),
            );

            $zoneId = $this->syncCommandBus->handle($command);
            $io->success("Zone moved: ".$zoneId." => ".$newParentZoneId);

            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error($this->exceptionMessageManager->cliResponseFormat($e));

            return Command::FAILURE;
        }
    }
}
