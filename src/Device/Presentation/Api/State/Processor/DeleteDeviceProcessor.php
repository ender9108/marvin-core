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

namespace Marvin\Device\Presentation\Api\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Exception;
use Marvin\Device\Application\Command\Device\DeleteDevice;
use Marvin\Device\Presentation\Api\Resource\ReadDeviceResource;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;

final readonly class DeleteDeviceProcessor implements ProcessorInterface
{
    public function __construct(
        private SyncCommandBusInterface $syncCommandBus,
    ) {
    }

    /**
     * @param ReadDeviceResource $data
     * @throws Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        Assert::isInstanceOf($data, ReadDeviceResource::class);

        $this->syncCommandBus->handle(new DeleteDevice(
            DeviceId::fromString($data->id),
        ));
    }
}
