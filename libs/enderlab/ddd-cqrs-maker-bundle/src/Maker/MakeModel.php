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
use function sprintf;

class MakeModel extends AbstractMaker
{
    public function __construct(
        private readonly MakerService $makerService,
    ) {
    }

    public static function getCommandName(): string
    {
        return 'ddd-cqrs:make:model';
    }

    public static function getCommandDescription(): string
    {
        return 'Create a new model class (with, repository interface, repository, api resource)';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->addArgument(
                'domain',
                InputArgument::OPTIONAL,
                sprintf('Domain name of the model to create or update (e.g. <fg=yellow>%s</>)', Str::asClassName(Str::getRandomTerm()))
            )
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                sprintf('Class name of the model to create or update (e.g. <fg=yellow>%s</>)', Str::asClassName(Str::getRandomTerm()))
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
            $modelClassName = $input->getArgument('name');
            $domainName = $input->getArgument('domain');

            $this->makerService->setGenerator($generator);
            $this->makerService->setConsoleStyle($io);
            $modelInfos = $this->makerService->makeModel($domainName, $modelClassName);

            $io->success(sprintf('Model "%s" was created', $modelInfos['modelName']));
        } catch (Exception $e) {
            $io->error('File '.$e->getFile().' on line '.$e->getLine().': '.$e->getMessage());
        }
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
    }
}
