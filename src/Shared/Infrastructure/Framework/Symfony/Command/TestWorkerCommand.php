<?php

namespace Marvin\Shared\Infrastructure\Framework\Symfony\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:test:worker',
    description: 'Test worker',
)]
final readonly class TestWorkerCommand
{
    public function __invoke(SymfonyStyle $io): int
    {
        $count = 0;
        $loop = true;

        while ($loop) {
            $io->text('Worker is running ('.$count.')');
            sleep(10);
            $count++;

            if ($count === 100) {
                $loop = false;
            }
        }

        return Command::SUCCESS;
    }
}
