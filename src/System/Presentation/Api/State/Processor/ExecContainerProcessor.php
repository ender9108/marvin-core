<?php

/**
 * Marvin Core - DDD-based home automation system
 *
 * @package   Marvin\Core
 * @author    Alexandre Berthelot <alexandreberthelot9108@gmail.com>
 * @copyright 2024-present Alexandre Berthelot
 * @license   AGPL-3.0 License
 * @link      https://github.com/ender9108/marvin-core
 */

declare(strict_types=1);

namespace Marvin\System\Presentation\Api\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use EnderLab\DddCqrsBundle\Application\Command\CommandBusInterface;
use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Exception;
use Marvin\Shared\Domain\ValueObject\Identity\CorrelationId;
use Marvin\System\Application\Command\Container\ExecContainerCommand;
use Marvin\System\Domain\ValueObject\Identity\ContainerId;
use Marvin\System\Presentation\Api\Resource\ReadContainerResource;

final readonly class ExecContainerProcessor implements ProcessorInterface
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

        $this->commandBus->dispatch(new ExecContainerCommand(
            ContainerId::fromString($data->id),
            new CorrelationId(),
            20,
            $data->command,
            $data->args,
        ));

        return $data;
    }
}
