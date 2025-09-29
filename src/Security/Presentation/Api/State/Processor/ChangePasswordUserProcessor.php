<?php

namespace Marvin\Security\Presentation\Api\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Exception;
use Marvin\Security\Application\Command\User\ChangePasswordUser;
use Marvin\Security\Domain\ValueObject\Identity\UserId;
use Marvin\Security\Presentation\Api\Resource\User\ChangePasswordUserResource;
use Marvin\Security\Presentation\Api\Resource\User\ReadUserResource;
use Marvin\Security\Presentation\Api\Resource\User\UserResource;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

final readonly class ChangePasswordUserProcessor implements ProcessorInterface
{
    public function __construct(
        private ObjectMapperInterface $objectMapper,
        private SyncCommandBusInterface $syncCommandBus,
    ) {
    }

    /**
     * @param UserResource $data
     * @throws Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ReadUserResource
    {
        Assert::isInstanceOf($data, ChangePasswordUserResource::class);

        $data->id = $uriVariables['id'];

        $model = $this->syncCommandBus->handle(new ChangePasswordUser(
            new UserId($data->id),
            $data->currentPassword,
            $data->newPassword,
        ));

        return $this->objectMapper->map($model, ReadUserResource::class);
    }
}
