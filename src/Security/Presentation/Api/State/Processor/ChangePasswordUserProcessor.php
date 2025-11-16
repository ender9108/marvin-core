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

namespace Marvin\Security\Presentation\Api\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Exception;
use Marvin\Security\Application\Command\User\ChangePasswordUser;
use Marvin\Security\Presentation\Api\Dto\Input\ChangePasswordUserDto;
use Marvin\Security\Presentation\Api\Resource\ReadUserResource;
use Marvin\Shared\Domain\ValueObject\Identity\UserId;
use Symfonycasts\MicroMapper\MicroMapperInterface;

final readonly class ChangePasswordUserProcessor implements ProcessorInterface
{
    public function __construct(
        private MicroMapperInterface $microMapper,
        private SyncCommandBusInterface $syncCommandBus,
    ) {
    }

    /**
     * @param ChangePasswordUserDto $data
     * @throws Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ReadUserResource
    {
        Assert::isInstanceOf($data, ChangePasswordUserDto::class);

        $model = $this->syncCommandBus->handle(new ChangePasswordUser(
            new UserId($uriVariables['id']),
            $data->currentPassword,
            $data->newPassword,
        ));

        return $this->microMapper->map($model, ReadUserResource::class);
    }
}
