<?php

namespace Marvin\Security\Presentation\Api\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use EnderLab\DddCqrsBundle\Application\Command\CommandBusInterface;
use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Exception;
use Marvin\Security\Application\Command\User\RequestResetPasswordUser;
use Marvin\Security\Presentation\Api\Dto\Input\RequestResetPasswordUserDto;
use Marvin\Security\Presentation\Api\Resource\ReadUserResource;
use Marvin\Shared\Domain\ValueObject\Email;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

final readonly class RequestResetPasswordUserProcessor implements ProcessorInterface
{
    public function __construct(
        private ObjectMapperInterface $objectMapper,
        private CommandBusInterface $syncCommandBus,
    ) {
    }

    /**
     * @param RequestResetPasswordUserDto $data
     * @throws Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ReadUserResource
    {
        Assert::isInstanceOf($data, RequestResetPasswordUserDto::class);

        $this->syncCommandBus->dispatch(new RequestResetPasswordUser(
            new Email($data->email),
        ));

        return $context['previous_data'];
    }
}
