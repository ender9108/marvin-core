<?php
namespace Marvin\Security\Domain\Event\User;

use Marvin\Security\Domain\ValueObject\Identity\UserId;
use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;
use EnderLab\DddCqrsBundle\Domain\Event\DomainEventInterface;
use SensitiveParameter;

final readonly class UserPasswordUpdated extends AbstractDomainEvent implements DomainEventInterface
{
    public function __construct(
        public UserId $id,
        #[SensitiveParameter]
        public string $newPassword,
    ) {
        parent::__construct();
    }

    public static function getRoutingKey(): string
    {
        return '$.system.user.password_updated';
    }
}
