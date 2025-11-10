<?php

namespace Marvin\System\Presentation\Api\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use EnderLab\DddCqrsBundle\Application\Command\CommandBusInterface;
use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Exception;
use Marvin\Shared\Domain\ValueObject\Identity\CorrelationId;
use Marvin\System\Application\Command\Container\RestartContainer;
use Marvin\System\Application\Command\Container\StartContainer;
use Marvin\System\Domain\ValueObject\Identity\ContainerId;
use Marvin\System\Presentation\Api\Resource\ReadContainerResource;

final readonly class RestartContainerProcessor implements ProcessorInterface
{
    public function __construct(
        private CommandBusInterface $commandBus,
    ) {
    }

    /**
     * @param ReadContainerResource $data
     * @throws Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ReadContainerResource
    {
        Assert::isInstanceOf($data, ReadContainerResource::class);

        $this->commandBus->dispatch(new RestartContainer(
            ContainerId::fromString($data->id),
            new CorrelationId(),
            20
        ));

        return $data;
    }
}
