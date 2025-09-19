<?php

namespace Marvin\Security\Application\EventHandler;

use EnderLab\DddCqrsBundle\Application\Event\DomainEventHandlerInterface;
use EnderLab\DddCqrsBundle\Domain\Event\DomainEventInterface;
use Marvin\Security\Domain\Event\User\UserCreated;

class OnCreateUserHandler implements DomainEventHandlerInterface
{
    public function __invoke(UserCreated $event): void
    {
        // TODO: Implement __invoke() method.
    }

    public static function supports(DomainEventInterface $event): bool
    {
        // TODO: Implement supports() method.
    }
}
