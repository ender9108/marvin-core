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
use Symfonycasts\MicroMapper\MicroMapperInterface;

final readonly class ApiToEntityStateProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: PersistProcessor::class)]
        protected ProcessorInterface $persistProcessor,
        #[Autowire(service: RemoveProcessor::class)]
        protected ProcessorInterface $removeProcessor,
        private MicroMapperInterface $microMapper,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ?ApiResourceInterface
    {
        $stateOptions = $operation->getStateOptions();
        assert($stateOptions instanceof Options);

        $entityClass = $stateOptions->getEntityClass();
        assert($data instanceof ApiResourceInterface);

        if ($operation instanceof Put) {
            $data->id = $context['previous_data']->id;
            $context['previous_data'] = $this->microMapper->map($context['previous_data'], $entityClass, [
                MicroMapperInterface::MAX_DEPTH => 0
            ]);
        }

        $entity = $this->microMapper->map($data, $entityClass, [
            MicroMapperInterface::MAX_DEPTH => 0
        ]);

        if (!is_object($entity)) {
            throw new RuntimeException('Entity "'.$entityClass.'" is not object');
        }

        switch (true) {
            case $operation instanceof Put:
            case $operation instanceof Patch:
                if (method_exists($entity, 'update')) {
                    $entity->update($context['previous_data']);
                }
                break;
            case $operation instanceof DeleteOperationInterface:
                if (method_exists($entity, 'delete')) {
                    $entity->delete();
                }

                $this->removeProcessor->process($entity, $operation, $uriVariables, $context);
                return null;
            default:
        }

        $this->persistProcessor->process($entity, $operation, $uriVariables, $context);

        /** @var ApiResourceInterface $resource */
        $resource = $this->microMapper->map($entity, get_class($data), [
            MicroMapperInterface::MAX_DEPTH => 0
        ]);

        return $resource;
    }
}
