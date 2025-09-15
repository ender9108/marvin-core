<?php
namespace Marvin\Security\Presentation\Api\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Exception;
use Marvin\Security\Application\Command\User\ChangeUserPassword;
use Marvin\Security\Application\Command\User\CreateUser;
use Marvin\Security\Domain\ValueObject\Firstname;
use Marvin\Security\Domain\ValueObject\Identity\UserId;
use Marvin\Security\Domain\ValueObject\Lastname;
use Marvin\Security\Domain\ValueObject\Roles;
use Marvin\Security\Presentation\Api\Resource\User\ChangeUserPasswordResource;
use Marvin\Security\Presentation\Api\Resource\User\CreateUserResource;
use Marvin\Shared\Domain\ValueObject\Email;
use Marvin\Shared\Domain\ValueObject\Reference;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

final readonly class ChangeUserPasswordProcessor implements ProcessorInterface
{
    public function __construct(
        private ObjectMapperInterface $objectMapper,
        private SyncCommandBusInterface $commandBus,
    ) {
    }

    /**
     * @param ChangeUserPasswordResource $data
     * @throws Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): CreateUserResource
    {
        Assert::isInstanceOf($data, ChangeUserPasswordResource::class);

        $model = $this->commandBus->handle(new ChangeUserPassword(
            new UserId($data->id),
            $data->currentPassword,
            $data->password,
        ));

        return $this->objectMapper->map($model, CreateUserResource::class);
    }
}
