<?php

namespace Marvin\Location\Presentation\Cli;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use Exception;
use Marvin\Location\Application\Command\Zone\DeleteZone;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Presentation\Exception\Service\ExceptionMessageManager;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:location:delete-zone',
    description: 'Delete zone',
)]
final readonly class DeleteZoneCommand
{
    public function __construct(
        private SyncCommandBusInterface $syncCommandBus,
        private ExceptionMessageManager $exceptionMessageManager,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument(name: 'zoneId')]
        string $zoneId
    ): int {
        try {
            $command = new DeleteZone(ZoneId::fromString($zoneId));

            $this->syncCommandBus->handle($command);
            $io->success("Zone deleted: " . $zoneId);

            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error($this->exceptionMessageManager->cliResponseFormat($e));

            return Command::FAILURE;
        }
    }
}
