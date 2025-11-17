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
use Marvin\Secret\Application\Command\StoreSecret;
use Marvin\Secret\Domain\ValueObject\SecretCategory;
use Marvin\Secret\Domain\ValueObject\SecretKey;
use Marvin\Secret\Domain\ValueObject\SecretScope;
use Marvin\Secret\Presentation\Api\Dto\CreateSecretDto;
use Marvin\Secret\Presentation\Api\Resource\ReadSecretResource;
use Symfonycasts\MicroMapper\MicroMapperInterface;

final readonly class CreateSecretProcessor implements ProcessorInterface
{
    public function __construct(
        private MicroMapperInterface $microMapper,
        private SyncCommandBusInterface $syncCommandBus,
    ) {
    }

    /**
     * @var CreateSecretDto $data
     * @throws Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ReadSecretResource
    {
        Assert::isInstanceOf($data, CreateSecretDto::class);

        $secret = $this->syncCommandBus->handle(
            new StoreSecret(
                SecretKey::fromString($data->key),
                $data->value,
                SecretScope::from($data->scope),
                SecretCategory::from($data->category),
                $data->managed,
                $data->rotationIntervalDays,
                $data->autoRotate,
                $data->rotationCommand,
                $data->expiresAt,
                $data->metadata,
            )
        );

        return $this->microMapper->map($secret, ReadSecretResource::class);
    }
}
