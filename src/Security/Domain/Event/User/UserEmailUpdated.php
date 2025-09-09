<?php
namespace Marvin\Security\Domain\Event\User;

use Marvin\Security\Domain\ValueObject\Identity\UserId;
use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;
use EnderLab\DddCqrsBundle\Domain\Event\DomainEventInterface;
use Marvin\Shared\Domain\ValueObject\Email;

final readonly class UserEmailUpdated extends AbstractDomainEvent implements DomainEventInterface
{
    public function __construct(
        public UserId $id,
        public Email $newEmail
    ) {
        parent::__construct();
    }

    public static function getRoutingKey(): string
    {
        return '$.security.user.email_updated';
    }
}
