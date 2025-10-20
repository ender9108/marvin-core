<?php

namespace Marvin\Security\Domain\Event\User;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;
use EnderLab\DddCqrsBundle\Domain\Event\DomainEventInterface;
use Marvin\Shared\Domain\ValueObject\Email;
use Marvin\Shared\Domain\ValueObject\Identity\UserId;

final readonly class UserEmailUpdated extends AbstractDomainEvent implements DomainEventInterface
{
    public function __construct(
        public UserId $id,
        public Email $newEmail
    ) {
        parent::__construct();
    }

    public function getEventName(): string
    {
        return '$.security.user.email_updated';
    }
}
