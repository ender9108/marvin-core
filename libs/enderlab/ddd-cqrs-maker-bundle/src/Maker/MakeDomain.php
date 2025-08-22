<?php

namespace EnderLab\DddCqrsMakerBundle\Maker;

use EnderLab\DddCqrsMakerBundle\Service\MakerService;
use Exception;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

class MakeDomain extends AbstractMaker
{
    public function __construct(
        private readonly MakerService $makerService,
    ) {
    }

    public static function getCommandName(): string
    {
        return 'ddd-cqrs:make:domain';
    }

    public static function getCommandDescription(): string
    {
        return 'Create a new domain';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->addArgument(
                'domain',
                InputArgument::OPTIONAL,
                sprintf('Domain name of the model to create or update (e.g. <fg=yellow>%s</>)', Str::asClassName(Str::getRandomTerm()))
            )
        ;

        $inputConfig->setArgumentAsNonInteractive('domain');
        $inputConfig->setArgumentAsNonInteractive('name');
    }

    /**
     * @throws Exception
     */
    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        try {
            $domainName = $input->getArgument('domain');

            $this->makerService->setGenerator($generator);
            $this->makerService->setConsoleStyle($io);
            $domainName = $this->makerService->makeDomain($domainName);

            $io->success(sprintf('Domain "%s" was created', $domainName));
        } catch (Exception $e) {
            $io->error('File '.$e->getFile().' on line '.$e->getLine().': '.$e->getMessage());
        }

        /*
         * $domain = MakerHelper::domainQuestion($io, false);

        MakerHelper::makeDomain($io, $domain);
         */
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
    }
}
