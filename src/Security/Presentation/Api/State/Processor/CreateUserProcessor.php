<?php
namespace Marvin\Security\Presentation\Api\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use EnderLab\DddCqrsBundle\Domain\Assert;
use Exception;
use Marvin\Security\Application\Command\User\CreateUser;
use Marvin\Security\Domain\List\Role;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\ValueObject\Firstname;
use Marvin\Security\Domain\ValueObject\Lastname;
use Marvin\Security\Domain\ValueObject\Roles;
use Marvin\Security\Presentation\Api\Resource\User\PostUserResource;
use Marvin\Shared\Domain\ValueObject\Email;
use Marvin\Shared\Domain\ValueObject\Reference;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

final readonly class CreateUserProcessor implements ProcessorInterface
{
    public function __construct(
        private ObjectMapperInterface $objectMapper,
        private SyncCommandBusInterface $commandBus,
    ) {
    }

    /**
     * @param PostUserResource $data
     * @throws Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): PostUserResource
    {
        Assert::isInstanceOf($data, PostUserResource::class);

        $model = $this->commandBus->handle(new CreateUser(
            new Email($data->email),
            new Firstname($data->firstname),
            new Lastname($data->lastname),
            Roles::user(),
            new Reference($data->type),
            $data->password,
        ));

        return $this->objectMapper->map($model, PostUserResource::class);
    }
}
