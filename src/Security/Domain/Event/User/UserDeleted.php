<?php

namespace Marvin\Security\Domain\Event\User;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;
use EnderLab\DddCqrsBundle\Domain\Event\DomainEventInterface;
use Marvin\Security\Domain\ValueObject\Identity\UserId;
use Marvin\Security\Domain\ValueObject\UserType;
use Marvin\Shared\Domain\ValueObject\Email;

final readonly class UserDeleted extends AbstractDomainEvent implements DomainEventInterface
{
    public function __construct(
        public UserId $id,
        public UserType $type,
        public Email $email
    ) {
        parent::__construct();
    }

    public static function getRoutingKey(): string
    {
        return '$.security.user.deleted';
    }
}
