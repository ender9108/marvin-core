<?php

namespace Marvin\Security\Presentation\Api\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Exception;
use Marvin\Security\Application\Command\User\ChangeUserEmail;
use Marvin\Security\Domain\ValueObject\Identity\UserId;
use Marvin\Security\Presentation\Api\Resource\User\ChangeUserEmailResource;
use Marvin\Security\Presentation\Api\Resource\User\GetUserResource;
use Marvin\Shared\Domain\ValueObject\Email;
use Symfonycasts\MicroMapper\MicroMapperInterface;

final readonly class ChangeUserEmailProcessor implements ProcessorInterface
{
    public function __construct(
        private MicroMapperInterface $microMapper,
        private SyncCommandBusInterface $commandBus,
    ) {
    }

    /**
     * @param ChangeUserEmailResource $data
     * @throws Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): GetUserResource
    {
        Assert::isInstanceOf($data, ChangeUserEmailResource::class);

        $data->id = $uriVariables['id'];

        $model = $this->commandBus->handle(new ChangeUserEmail(
            new UserId($data->id),
            new Email($data->email),
        ));

        return $this->microMapper->map($model, GetUserResource::class);
    }
}
