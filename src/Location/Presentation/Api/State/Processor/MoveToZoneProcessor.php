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

namespace Marvin\Location\Presentation\Api\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Exception;
use Marvin\Location\Application\Command\Zone\MoveZone;
use Marvin\Location\Presentation\Api\Dto\Input\MoveToZoneDto;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;

final readonly class MoveToZoneProcessor implements ProcessorInterface
{
    public function __construct(
        private SyncCommandBusInterface $syncCommandBus,
    ) {
    }


    /**
     * @param MoveToZoneDto $data
     * @throws Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        Assert::isInstanceOf($data, MoveToZoneDto::class);

        $this->syncCommandBus->handle(
            new MoveZone(
                ZoneId::fromString($uriVariables['id']),
                ZoneId::fromString($data->newParentZoneId)
            )
        );

        return $data;
    }
}
