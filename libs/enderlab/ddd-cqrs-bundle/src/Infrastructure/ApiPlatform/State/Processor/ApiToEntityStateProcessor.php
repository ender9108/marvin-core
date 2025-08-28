<?php

namespace EnderLab\DddCqrsBundle\Infrastructure\ApiPlatform\State\Processor;

use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use ApiPlatform\Doctrine\Common\State\RemoveProcessor;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;
use EnderLab\DddCqrsBundle\Infrastructure\ApiPlatform\ApiResourceInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

readonly class ApiToEntityStateProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: PersistProcessor::class)]
        protected ProcessorInterface $persistProcessor,
        #[Autowire(service: RemoveProcessor::class)]
        protected ProcessorInterface $removeProcessor,
        protected ObjectMapperInterface $objectMapper,
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
            $context['previous_data'] = $this->objectMapper->map($context['previous_data'], $entityClass);
        }

        $entity = $this->objectMapper->map($data, $entityClass);

        if ($operation instanceof DeleteOperationInterface) {
            $this->removeProcessor->process($entity, $operation, $uriVariables, $context);
            return null;
        }

        $this->persistProcessor->process($entity, $operation, $uriVariables, $context);

        /** @var ApiResourceInterface $resource */
        $resource = $this->objectMapper->map($entity, get_class($data));

        return $resource;
    }
}
