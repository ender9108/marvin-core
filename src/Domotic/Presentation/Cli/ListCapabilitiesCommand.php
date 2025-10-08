<?php

namespace Marvin\Domotic\Presentation\Cli;

use EnderLab\DddCqrsBundle\Application\Query\QueryBusInterface;
use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use Marvin\Shared\Presentation\Exception\Service\ExceptionMessageManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:domotic:list-capabilities',
    description: 'List of device capabilities',
)]
final readonly class ListCapabilitiesCommand
{
    public function __construct(
        private QueryBusInterface $queryBus,
        private ExceptionMessageManager $exceptionMessageManager,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
    ): int {
        try {
            Assert::string(1);

            return Command::SUCCESS;
        } catch (DomainException $de) {
            $io->error($this->exceptionMessageManager->cliResponseFormat($de));

            return Command::FAILURE;
        }
    }
}
