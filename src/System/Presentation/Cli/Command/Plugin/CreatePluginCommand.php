<?php

namespace Marvin\System\Presentation\Cli\Command\Plugin;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use Marvin\Shared\Domain\ValueObject\Description;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Metadata;
use Marvin\Shared\Domain\ValueObject\Reference;
use Marvin\Shared\Presentation\Exception\Service\ExceptionMessageManager;
use Marvin\System\Application\Command\CreatePlugin;
use Marvin\System\Application\Command\EnablePlugin;
use Marvin\System\Domain\List\PluginStatusReference;
use Marvin\System\Domain\ValueObject\Identity\PluginId;
use Marvin\System\Domain\ValueObject\Version;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:system:create-plugin',
    description: 'Create plugin',
)]
final readonly class CreatePluginCommand
{
    public function __construct(
        private SyncCommandBusInterface $syncCommandBus,
        private ExceptionMessageManager $exceptionMessageManager,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument(name: 'label')]
        string $label,
        #[Argument(name: 'reference')]
        string $reference,
        #[Argument(name: 'version')]
        string $version,
        #[Argument(name: 'statusReference')]
        string $statusReference = PluginStatusReference::STATUS_ENABLED->value,
        #[Argument(name: 'description')]
        ?string $description = null,
        #[Argument(name: 'metadata')]
        array $metadata = [],
    ): int {
        try {
            $this->syncCommandBus->handle(new CreatePlugin(
                new Label($label),
                new Reference($reference),
                new Version($version),
                new Reference($statusReference),
                new Metadata($metadata),
                null !== $description ? new Description($description) : null
            ));

            return Command::SUCCESS;
        } catch (DomainException $de) {
            $io->error($this->exceptionMessageManager->cliResponseFormat($de));

            return Command::FAILURE;
        }
    }
}
