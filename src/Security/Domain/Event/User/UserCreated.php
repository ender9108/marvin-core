<?php

namespace Marvin\Security\Domain\Event\User;

use DateTimeImmutable;
use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;
use EnderLab\DddCqrsBundle\Domain\Event\DomainEventInterface;
use Marvin\Shared\Domain\ValueObject\Identity\UserId;
use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage(transport: 'domain.event')]
final readonly class UserCreated extends AbstractDomainEvent implements DomainEventInterface
{
    public function __construct(public UserId $id)
    {
        parent::__construct();
    }

    public function getEventName(): string
    {
        return '$.security.user.created';
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->id->toString(),
        ];
    }
}
