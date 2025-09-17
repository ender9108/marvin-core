<?php

namespace Marvin\Security\Domain\Event\User;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;
use EnderLab\DddCqrsBundle\Domain\Event\DomainEventInterface;
use Marvin\Security\Domain\ValueObject\Identity\UserId;

final readonly class UserEnabled extends AbstractDomainEvent implements DomainEventInterface
{
    public function __construct(public UserId $id)
    {
        parent::__construct();
    }

    public static function getRoutingKey(): string
    {
        return '$.security.user.enabled';
    }
}
