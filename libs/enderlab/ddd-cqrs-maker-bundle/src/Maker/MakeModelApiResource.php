<?php

namespace EnderLab\DddCqrsMakerBundle\Maker;

use ApiPlatform\Metadata\ApiResource;
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

class MakeModelApiResource extends AbstractMaker
{
    public function __construct(
        private readonly MakerService $makerService,
    ) {
    }

    public static function getCommandName(): string
    {
        return 'ddd-cqrs:make:api-resource';
    }

    public static function getCommandDescription(): string
    {
        return 'Create a new ApiResource class';
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
        if (!class_exists(ApiResource::class)) {
            $io->warning('You must install ApiPlatform to use this command.');
            return;
        }

        $modelClassName = $input->getArgument('name');
        $domainName = $input->getArgument('domain');

        $this->makerService->setGenerator($generator);
        $this->makerService->setConsoleStyle($io);

        $this->makerService->makeApiResource($domainName, $modelClassName);

        $io->success(sprintf('ApiResource %s was created', $modelClassName));
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
    }
}
