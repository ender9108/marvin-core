<?php

namespace Marvin\System\Presentation\Cli\Command\Plugin;

use EnderLab\DddCqrsBundle\Application\Query\QueryBusInterface;
use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use Marvin\Shared\Presentation\Exception\Service\ExceptionMessageManager;
use Marvin\System\Application\Query\GetPluginsCollection;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsCommand(
    name: 'marvin:system:list-plugins',
    description: 'List of plugins',
)]
final readonly class ListPluginsCommand
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
            $plugins = $this->queryBus->handle(new GetPluginsCollection());
            $headers = ['id', 'label', 'reference', 'version', 'status', 'metadata', 'description'];
            $rows = [];

            foreach ($plugins as $plugin) {
                $rows[] = [
                    $plugin->id,
                    $this->translator->trans($plugin->label->value, [], 'domotic', $locale),
                    $plugin->reference,
                    $plugin->version,
                    $plugin->status->value,
                    json_encode($plugin->metadata->value, JSON_PRETTY_PRINT),
                    $plugin->description?->value,
                ];
            }

            switch ($format) {
                case 'json':
                    $io->writeln(json_encode($rows, JSON_PRETTY_PRINT));
                    break;
                case 'table':
                    $io->table($headers, $rows);
                    break;
            }

            return Command::SUCCESS;
        } catch (DomainException $de) {
            $io->error($this->exceptionMessageManager->cliResponseFormat($de));

            return Command::FAILURE;
        }
    }
}
