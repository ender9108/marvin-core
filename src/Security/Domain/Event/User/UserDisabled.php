<?php
namespace Marvin\Security\Domain\Event\User;

use Marvin\Security\Domain\ValueObject\Identity\UserId;
use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;
use EnderLab\DddCqrsBundle\Domain\Event\DomainEventInterface;

final readonly class UserDisabled extends AbstractDomainEvent implements DomainEventInterface
{
    public function __construct(public UserId $id) {
        parent::__construct();
    }

    public static function getRoutingKey(): string
    {
        return '$.system.user.disabled';
    }
}
