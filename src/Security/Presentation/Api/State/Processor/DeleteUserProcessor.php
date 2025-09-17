<?php

namespace Marvin\Security\Presentation\Api\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Exception;
use Marvin\Security\Application\Command\User\CreateUser;
use Marvin\Security\Application\Command\User\DeleteUser;
use Marvin\Security\Domain\ValueObject\Firstname;
use Marvin\Security\Domain\ValueObject\Identity\UserId;
use Marvin\Security\Domain\ValueObject\Lastname;
use Marvin\Security\Domain\ValueObject\Roles;
use Marvin\Security\Presentation\Api\Resource\User\CreateUserResource;
use Marvin\Security\Presentation\Api\Resource\User\DeleteUserResource;
use Marvin\Shared\Domain\ValueObject\Email;
use Marvin\Shared\Domain\ValueObject\Reference;
use Symfonycasts\MicroMapper\MicroMapperInterface;

final readonly class DeleteUserProcessor implements ProcessorInterface
{
    public function __construct(
        private SyncCommandBusInterface $commandBus,
    ) {
    }

    /**
     * @param DeleteUserResource $data
     * @throws Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        Assert::isInstanceOf($data, DeleteUserResource::class);

        $this->commandBus->handle(
            new DeleteUser(new UserId($uriVariables['id']))
        );
    }
}
