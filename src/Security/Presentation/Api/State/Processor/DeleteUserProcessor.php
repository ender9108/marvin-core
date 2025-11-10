<?php

namespace Marvin\Security\Presentation\Api\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use Exception;
use Marvin\Security\Application\Command\User\DeleteUser;
use Marvin\Shared\Domain\ValueObject\Identity\UserId;

final readonly class DeleteUserProcessor implements ProcessorInterface
{
    public function __construct(
        private SyncCommandBusInterface $syncCommandBus,
    ) {
    }

    /**
     * @throws Exception
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $this->syncCommandBus->handle(
            new DeleteUser(new UserId($uriVariables['id']))
        );

        return $data;
    }
}
