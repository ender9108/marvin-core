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

namespace Marvin\Secret\Presentation\Api\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use EnderLab\DddCqrsBundle\Application\Command\CommandBusInterface;
use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Exception;
use Marvin\Secret\Application\Command\RotateSecret;
use Marvin\Secret\Domain\ValueObject\SecretKey;
use Marvin\Secret\Presentation\Api\Dto\RotateSecretDto;
use Marvin\Secret\Presentation\Api\Resource\ReadSecretResource;
use Symfonycasts\MicroMapper\MicroMapperInterface;

final readonly class RotateSecretProcessor implements ProcessorInterface
{
    public function __construct(
        private MicroMapperInterface $microMapper,
        private CommandBusInterface $commandBus,
    ) {
    }

    /**
     * @var RotateSecretDto $data
     * @throws Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ReadSecretResource
    {
        Assert::isInstanceOf($data, RotateSecretDto::class);

        $secret = $this->commandBus->dispatch(
            new RotateSecret(
                SecretKey::fromString($data->key),
                $data->newValue,
            )
        );

        return $this->microMapper->map($secret, ReadSecretResource::class);
    }
}
