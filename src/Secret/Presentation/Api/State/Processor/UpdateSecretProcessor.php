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
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Exception;
use Marvin\Secret\Application\Command\UpdateSecret;
use Marvin\Secret\Domain\ValueObject\SecretKey;
use Marvin\Secret\Presentation\Api\Dto\UpdateSecretDto;
use Marvin\Secret\Presentation\Api\Resource\ReadSecretResource;
use Symfonycasts\MicroMapper\MicroMapperInterface;

final readonly class UpdateSecretProcessor implements ProcessorInterface
{
    public function __construct(
        private MicroMapperInterface $microMapper,
        private SyncCommandBusInterface $syncCommandBus,
    ) {
    }

    /**
     * @var UpdateSecretDto $data
     * @throws Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ReadSecretResource
    {
        Assert::isInstanceOf($data, UpdateSecretDto::class);

        $secret = $this->syncCommandBus->handle(
            new UpdateSecret(
                SecretKey::fromString($data->key),
                $data->value,
            )
        );

        return $this->microMapper->map($secret, ReadSecretResource::class);
    }
}
