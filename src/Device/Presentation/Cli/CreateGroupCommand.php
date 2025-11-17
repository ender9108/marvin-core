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

namespace Marvin\Device\Presentation\Cli;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use Exception;
use Marvin\Device\Application\Command\Group\CreateGroup;
use Marvin\Device\Domain\ValueObject\CompositeStrategy;
use Marvin\Device\Domain\ValueObject\ExecutionStrategy;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Presentation\Exception\Service\ExceptionMessageManager;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:device:create-group',
    description: 'Create a device group',
)]
final readonly class CreateGroupCommand
{
    public function __construct(
        private SyncCommandBusInterface $syncCommandBus,
        private ExceptionMessageManager $exceptionMessageManager,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument(description: 'Group label', name: 'label')]
        string $label,
        #[Argument(description: 'Device IDs (comma-separated)', name: 'device-ids')]
        string $deviceIds,
        #[Option(description: 'Composite strategy (native_if_available, native_only, emulated)', name: 'composite-strategy')]
        string $compositeStrategy = 'native_if_available',
        #[Option(description: 'Execution strategy (broadcast, sequential, optimized)', name: 'execution-strategy')]
        string $executionStrategy = 'broadcast',
    ): int {
        try {
            $deviceIdArray = array_map(
                fn (string $id) => new DeviceId(trim($id)),
                explode(',', $deviceIds)
            );

            $groupId = $this->syncCommandBus->handle(new CreateGroup(
                label: Label::fromString($label),
                childrenDeviceIds: $deviceIdArray,
                compositeStrategy: CompositeStrategy::from($compositeStrategy),
                executionStrategy: ExecutionStrategy::from($executionStrategy),
            ));

            $io->success(sprintf('Group "%s" created successfully with ID: %s', $label, $groupId));

            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error($this->exceptionMessageManager->cliResponseFormat($e));

            return Command::FAILURE;
        }
    }
}
