<?php

namespace Marvin\Security\Presentation\Cli;

use EnderLab\DddCqrsBundle\Application\Query\QueryBusInterface;
use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use Marvin\Security\Application\Query\GetUsersCollection;
use Marvin\Security\Domain\Model\User;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:security:list-users',
    description: 'Get users list',
)]
final readonly class ListUserCommand
{
    public function __construct(
        private QueryBusInterface $queryBus,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Option('page')]
        int $page = 1,
        #[Option('items-per-page')]
        int $itemsPerPage = 20,
    ): int {
        try {
            /** @var User[] $users */
            $users = $this->queryBus->handle(new GetUsersCollection(
                [],
                ['firstname.firstname' => 'asc'],
                $page,
                $itemsPerPage,
            ));
            $table = $io->createTable();
            $table->setHeaders(['Id', 'Email', 'Firstname', 'Lastname', 'Roles', 'Type', 'Status', 'Created At']);

            foreach ($users as $user) {
                $table->addRow([
                    $user->id->toString(),
                    $user->email,
                    $user->firstname,
                    $user->lastname,
                    implode(', ', $user->roles->toArray()),
                    $user->type->reference,
                    $user->status->reference,
                    $user->createdAt->value->format('Y-m-d H:i:s'),
                ]);
            }

            $table->render();

            return Command::SUCCESS;
        } catch (DomainException $de) {
            $io->error($de->getMessage());

            return Command::FAILURE;
        }
    }
}
