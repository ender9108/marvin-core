<?php

namespace Marvin\Security\Presentation\Api\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use EnderLab\DddCqrsBundle\Application\Command\CommandBusInterface;
use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use Exception;
use Marvin\Security\Application\Command\User\ResetPasswordUser;
use Marvin\Security\Presentation\Api\Dto\Input\RequestResetPasswordUserDto;
use Marvin\Security\Presentation\Api\Resource\ReadUserResource;

final readonly class ResetPasswordUserProcessor implements ProcessorInterface
{
    public function __construct(
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

        $this->syncCommandBus->dispatch(new ResetPasswordUser(
            $data->token,
            $data->password,
        ));

        return $context['previous_data'];
    }
}
