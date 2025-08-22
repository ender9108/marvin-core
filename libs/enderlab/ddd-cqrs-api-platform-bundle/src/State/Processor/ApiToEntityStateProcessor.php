<?php

namespace EnderLab\DddCqrsApiPlatformBundle\State\Processor;

use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use ApiPlatform\Doctrine\Common\State\RemoveProcessor;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;
use EnderLab\DddCqrsApiPlatformBundle\ApiResourceInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfonycasts\MicroMapper\MicroMapperInterface;

readonly class ApiToEntityStateProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: PersistProcessor::class)]
        private ProcessorInterface $persistProcessor,
        #[Autowire(service: RemoveProcessor::class)]
        private ProcessorInterface $removeProcessor,
        private MicroMapperInterface $microMapper,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ?ApiResourceInterface
    {
        $stateOptions = $operation->getStateOptions();
        assert($stateOptions instanceof Options);

        $entityClass = $stateOptions->getEntityClass();
        assert($data instanceof ApiResourceInterface);

        if ($operation instanceof Put) {
            $data->id = $context['previous_data']->id;
            $context['previous_data'] = $this->microMapper->map($context['previous_data'], $entityClass);
        }

        $entity = $this->microMapper->map($data, $entityClass);

        if ($operation instanceof DeleteOperationInterface) {
            $this->removeProcessor->process($entity, $operation, $uriVariables, $context);
            return null;
        }

        $this->persistProcessor->process($entity, $operation, $uriVariables, $context);

        return $this->microMapper->map($entity, get_class($data));
    }
}
