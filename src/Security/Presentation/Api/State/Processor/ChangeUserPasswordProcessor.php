<?php

namespace Marvin\Security\Presentation\Api\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Exception;
use Marvin\Security\Application\Command\User\ChangeUserPassword;
use Marvin\Security\Domain\ValueObject\Identity\UserId;
use Marvin\Security\Presentation\Api\Resource\User\ChangeUserPasswordResource;
use Marvin\Security\Presentation\Api\Resource\User\GetUserResource;
use Symfonycasts\MicroMapper\MicroMapperInterface;

final readonly class ChangeUserPasswordProcessor implements ProcessorInterface
{
    public function __construct(
        private MicroMapperInterface $microMapper,
        private SyncCommandBusInterface $commandBus,
    ) {
    }

    /**
     * @param ChangeUserPasswordResource $data
     * @throws Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): GetUserResource
    {
        Assert::isInstanceOf($data, ChangeUserPasswordResource::class);

        $data->id = $uriVariables['id'];

        $model = $this->commandBus->handle(new ChangeUserPassword(
            new UserId($data->id),
            $data->currentPassword,
            $data->newPassword,
        ));

        return $this->microMapper->map($model, GetUserResource::class);
    }
}
