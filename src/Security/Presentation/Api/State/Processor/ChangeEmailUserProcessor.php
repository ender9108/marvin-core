<?php

namespace Marvin\Security\Presentation\Api\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Exception;
use Marvin\Security\Application\Command\User\ChangeEmailUser;
use Marvin\Security\Presentation\Api\Dto\Input\ChangeEmailUserDto;
use Marvin\Security\Presentation\Api\Resource\ReadUserResource;
use Marvin\Shared\Domain\ValueObject\Email;
use Marvin\Shared\Domain\ValueObject\Identity\UserId;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

final readonly class ChangeEmailUserProcessor implements ProcessorInterface
{
    public function __construct(
        private ObjectMapperInterface $objectMapper,
        private SyncCommandBusInterface $syncCommandBus,
    ) {
    }

    /**
     * @param ChangeEmailUserDto $data
     * @throws Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ReadUserResource
    {
        Assert::isInstanceOf($data, ChangeEmailUserDto::class);

        $model = $this->syncCommandBus->handle(new ChangeEmailUser(
            new UserId($uriVariables['id']),
            new Email($data->email),
        ));

        return $this->objectMapper->map($model, ReadUserResource::class);
    }
}
