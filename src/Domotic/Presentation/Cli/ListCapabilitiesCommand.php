<?php

namespace Marvin\Domotic\Presentation\Cli;

use EnderLab\DddCqrsBundle\Application\Query\QueryBusInterface;
use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use Marvin\Domotic\Application\Query\Device\GetCapabilitiesCollection;
use Marvin\Shared\Presentation\Exception\Service\ExceptionMessageManager;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsCommand(
    name: 'marvin:domotic:list-capabilities',
    description: 'List of device capabilities',
)]
final readonly class ListCapabilitiesCommand
{
    public function __construct(
        private QueryBusInterface $queryBus,
        private ExceptionMessageManager $exceptionMessageManager,
        private TranslatorInterface $translator,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument(description: 'Format of the response (json, table) [default: table]', name: 'format')]
        string $format = 'table',
        #[Argument(name: 'locale')]
        string $locale = 'fr',
    ): int {
        try {
            $capabilities = $this->queryBus->handle(new GetCapabilitiesCollection());
            $headers = ['id', 'label', 'reference'];
            $rows = [];

            foreach ($capabilities as $capability) {
                $rows[] = [
                    $capability->id,
                    $this->translator->trans($capability->label->value, [], 'domotic', $locale),
                    $capability->reference
                ];
            }

            match ($format) {
                'json' => $io->writeln(json_encode($rows, JSON_PRETTY_PRINT)),
                'table' => $io->table($headers, $rows),
                default => Command::SUCCESS,
            };

            return Command::SUCCESS;
        } catch (DomainException $de) {
            $io->error($this->exceptionMessageManager->cliResponseFormat($de));

            return Command::FAILURE;
        }
    }
}
