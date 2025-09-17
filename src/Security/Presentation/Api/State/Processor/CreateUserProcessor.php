<?php

namespace Marvin\Security\Presentation\Api\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Exception;
use Marvin\Security\Application\Command\User\CreateUser;
use Marvin\Security\Domain\ValueObject\Firstname;
use Marvin\Security\Domain\ValueObject\Lastname;
use Marvin\Security\Domain\ValueObject\Roles;
use Marvin\Security\Presentation\Api\Resource\User\UserResource;
use Marvin\Shared\Domain\ValueObject\Email;
use Marvin\Shared\Domain\ValueObject\Reference;
use Symfonycasts\MicroMapper\MicroMapperInterface;

final readonly class CreateUserProcessor implements ProcessorInterface
{
    public function __construct(
        private MicroMapperInterface $microMapper,
        private SyncCommandBusInterface $commandBus,
    ) {
    }

    /**
     * @param UserResource $data
     * @throws Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): UserResource
    {
        Assert::isInstanceOf($data, UserResource::class);

        $model = $this->commandBus->handle(new CreateUser(
            new Email($data->email),
            new Firstname($data->firstname),
            new Lastname($data->lastname),
            Roles::fromArray($data->roles),
            new Reference($data->type->reference),
            $data->password,
        ));

        return $this->microMapper->map($model, UserResource::class);
    }
}
