<?php

namespace EnderLab\DddCqrsApiPlatformBundle\Infrastructure\Framework\ApiPlatform\State\Processor;

use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use ApiPlatform\Doctrine\Common\State\RemoveProcessor;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;
use EnderLab\DddCqrsApiPlatformBundle\Infrastructure\Framework\ApiPlatform\ApiResourceInterface;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

final readonly class ApiToEntityStateProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: PersistProcessor::class)]
        protected ProcessorInterface $persistProcessor,
        #[Autowire(service: RemoveProcessor::class)]
        protected ProcessorInterface $removeProcessor,
        private MicroMapperInterface $microMapper,
        private ObjectMapperInterface $objectMapper,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ?object
    {
        $stateOptions = $operation->getStateOptions();
        assert($stateOptions instanceof Options);

        $entityClass = $stateOptions->getEntityClass();
        assert($data instanceof ApiResourceInterface);

        if ($operation instanceof Put) {
            $data->id = $context['previous_data']->id;
            $context['previous_data'] = $this->microMapper->map($context['previous_data'], $entityClass);
        }

        $entity = $this->objectMapper->map($data, $entityClass);

        if (!is_object($entity)) {
            throw new RuntimeException('Entity "'.$entityClass.'" is not object');
        }

        if ($operation instanceof DeleteOperationInterface) {
            if (method_exists($entity, 'delete')) {
                $entity->delete();
            }

            $this->removeProcessor->process($entity, $operation, $uriVariables, $context);
            return null;
        }

        $this->persistProcessor->process($entity, $operation, $uriVariables, $context);

        return $this->microMapper->map($entity, get_class($data), [
            MicroMapperInterface::MAX_DEPTH => 0
        ]);
    }
}
