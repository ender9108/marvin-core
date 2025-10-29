<?php

namespace Marvin\Secret\Presentation\Cli\Command;

use _PHPStan_6597ef616\Symfony\Component\Console\Question\ConfirmationQuestion;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use Exception;
use Marvin\Secret\Application\Command\DeleteSecret;
use Marvin\Secret\Domain\ValueObject\SecretKey;
use Marvin\Shared\Presentation\Exception\Service\ExceptionMessageManager;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'marvin:secrets:delete',
    description: 'Delete a secret',
    help: <<<HELP
Delete a secret permanently.

Example:
  php bin/console marvin:secrets:delete old_api_key
HELP
)]
final class DeleteSecretCommand extends Command
{
    public function __construct(
        private readonly SyncCommandBusInterface $syncCommandBus,
        private readonly ExceptionMessageManager $exceptionMessageManager,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('key', InputArgument::REQUIRED, 'Secret key to delete');
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {
        $io = new SymfonyStyle($input, $output);

        try {
            $key = $input->getArgument('key');

            $helper = new QuestionHelper();
            $question = new ConfirmationQuestion(
                sprintf(
                    'Are you sure you want to delete secret %s? This cannot be undone. [y/N] ',
                    $key
                ),
                false
            );

            if (!$helper->ask($input, $output, $question)) {
                $io->info('Operation cancelled');
                return Command::SUCCESS;
            }


            $this->syncCommandBus->handle(new DeleteSecret(new SecretKey($key)));
            $io->success("✅ Secret '{$key}' deleted successfully!");
        } catch (Exception $e) {
            $io->error($this->exceptionMessageManager->cliResponseFormat($e));
            return Command::FAILURE;
        }
    }
}
