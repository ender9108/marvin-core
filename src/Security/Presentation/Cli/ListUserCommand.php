<?php
namespace Marvin\Security\Presentation\Cli;

use EnderLab\DddCqrsBundle\Application\Query\QueryBusInterface;
use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:security:list-users',
    description: 'Get user list',
)]
final readonly class ListUserCommand
{
    public function __construct(
        private QueryBusInterface $queryBus,
    ) {
    }

    public function __invoke(SymfonyStyle $io): int {
        try {


            return Command::SUCCESS;
        } catch (DomainException $de) {
            $io->error($de->getMessage());

            return Command::FAILURE;
        }
    }
}
