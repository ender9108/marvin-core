<?php

namespace Marvin\Security\Domain\Event\User;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;
use EnderLab\DddCqrsBundle\Domain\Event\DomainEventInterface;
use Marvin\Shared\Domain\ValueObject\Identity\UserId;

final readonly class UserDisabled extends AbstractDomainEvent implements DomainEventInterface
{
    public function __construct(public UserId $id)
    {
        parent::__construct();
    }

    public function getEventName(): string
    {
        return '$.security.user.disabled';
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->id->toString(),
        ];
    }
}
