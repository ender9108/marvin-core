<?php

namespace App\System\Infrastructure\ApiPlatform\State\Processor;

use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use ApiPlatform\Doctrine\Common\State\RemoveProcessor;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;
use App\System\Application\Command\DeletePluginCommand;
use App\System\Application\Command\DisablePluginCommand;
use App\System\Application\Command\EnablePluginCommand;
use App\System\Domain\Model\PluginStatus;
use App\System\Infrastructure\ApiPlatform\Resource\PluginResource;
use App\System\Infrastructure\ApiPlatform\Resource\PluginStatusResource;
use EnderLab\DddCqrsBundle\Application\Command\Bus\CommandBus;
use EnderLab\DddCqrsBundle\Infrastructure\ApiPlatform\State\Processor\ApiToEntityStateProcessor;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

readonly class UpdatePluginProcessor extends ApiToEntityStateProcessor
{
    public function __construct(
        #[Autowire(service: PersistProcessor::class)]
        ProcessorInterface $persistProcessor,
        #[Autowire(service: RemoveProcessor::class)]
        ProcessorInterface $removeProcessor,
        ObjectMapperInterface $objectMapper,
        private CommandBus $commandBus,
    ) {
        parent::__construct($persistProcessor, $removeProcessor, $objectMapper);
    }

    /**
     * @throws ExceptionInterface
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): PluginResource
    {
        if (!$operation instanceof Put) {
            throw HttpException::fromStatusCode(403);
        }

        assert($data instanceof PluginResource);

        if ($data->status === $context['previous_data']->status) {
            return $data;
        }

        /** @var PluginResource $pluginResource */
        $pluginResource = parent::process($data, $operation, $uriVariables, $context);

        match ($pluginResource->status->reference) {
            PluginStatus::STATUS_DISABLED => $this->commandBus->dispatch(new DisablePluginCommand(
                $pluginResource->id,
                $pluginResource->reference
            )),
            PluginStatus::STATUS_TO_DELETE => $this->commandBus->dispatch(new DeletePluginCommand(
                $pluginResource->id,
                $pluginResource->reference
            )),
            PluginStatus::STATUS_ENABLED => $this->commandBus->dispatch(new EnablePluginCommand(
                $pluginResource->id,
                $pluginResource->reference
            )),
            default => null,
        };

        return $pluginResource;
    }
}
