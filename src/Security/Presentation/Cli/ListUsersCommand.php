<?php

namespace Marvin\Security\Presentation\Cli;

use EnderLab\DddCqrsBundle\Application\Query\QueryBusInterface;
use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Repository\PaginatorInterface;
use Marvin\Security\Application\Query\GetUsersCollection;
use Marvin\Security\Domain\Model\User;
use Marvin\Shared\Presentation\Exception\Service\ExceptionMessageManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:security:list-users',
    description: 'List all users',
)]
final readonly class ListUsersCommand
{
    public function __construct(
        private QueryBusInterface $queryBus,
        private ExceptionMessageManager $exceptionMessageManager,
    ) {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Option(name: 'page')]
        int $page = 1,
        #[Option(name: 'items-per-page')]
        int $itemsPerPage = 25,
    ): int {
        try {
            $query = new GetUsersCollection(
                page: $page,
                itemsPerPage: $itemsPerPage,
            );

            /** @var PaginatorInterface $users */
            $users = $this->queryBus->handle($query);

            if (empty($users)) {
                $io->info('No users found.');
                return Command::SUCCESS;
            }

            $rows = [];
            /** @var User $user */
            foreach ($users as $user) {
                $rows[] = [
                    $user->id->toString(),
                    $user->firstname->value . ' ' . $user->lastname->value,
                    $user->type->value,
                    $user->status->value,
                    $user->email,
                    $user->theme->value,
                    $user->locale->value,
                    $user->timezone->value,
                ];
            }

            $io->table(
                ['ID', 'Fullname', 'Type', 'Status', 'Email', 'Theme', 'Locale', 'Timezone'],
                $rows
            );

            $io->success(sprintf('Found %d zone(s).', count($users)));
            return Command::SUCCESS;
        } catch (DomainException $de) {
            $io->error($this->exceptionMessageManager->cliResponseFormat($de));

            return Command::FAILURE;
        } catch (\Exception $e) {
            $io->error("Failed: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
}
