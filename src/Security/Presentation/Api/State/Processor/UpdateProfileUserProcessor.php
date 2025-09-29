<?php

namespace Marvin\Security\Presentation\Api\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Exception;
use Marvin\Security\Application\Command\User\ChangeEmailUser;
use Marvin\Security\Application\Command\User\UpdateProfileUser;
use Marvin\Security\Domain\ValueObject\Firstname;
use Marvin\Security\Domain\ValueObject\Identity\UserId;
use Marvin\Security\Domain\ValueObject\Lastname;
use Marvin\Security\Presentation\Api\Resource\User\ReadUserResource;
use Marvin\Security\Presentation\Api\Resource\User\UpdateProfileUserResource;
use Marvin\Security\Presentation\Api\Resource\User\UserResource;
use Marvin\Shared\Domain\ValueObject\Email;
use Marvin\Shared\Domain\ValueObject\Locale;
use Marvin\Shared\Domain\ValueObject\Theme;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

final readonly class UpdateProfileUserProcessor implements ProcessorInterface
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
        Assert::isInstanceOf($data, UpdateProfileUserResource::class);

        $data->id = $uriVariables['id'];

        $model = $this->syncCommandBus->handle(new UpdateProfileUser(
            new UserId($data->id),
            null !== $data->firstname ? new Firstname($data->firstname) : null,
            null !== $data->lastname ? new Lastname($data->lastname) : null,
            null !== $data->theme ? new Theme($data->theme) : null,
            null !== $data->locale ? new Locale($data->locale) : null
        ));

        return $this->objectMapper->map($model, ReadUserResource::class);
    }
}
