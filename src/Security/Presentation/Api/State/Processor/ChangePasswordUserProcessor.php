<?php

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
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

final readonly class ChangePasswordUserProcessor implements ProcessorInterface
{
    public function __construct(
        private ObjectMapperInterface $objectMapper,
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

        return $this->objectMapper->map($model, ReadUserResource::class);
    }
}
