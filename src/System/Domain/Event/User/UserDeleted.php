<?php

namespace App\System\Domain\Event\User;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;
use EnderLab\DddCqrsBundle\Domain\Event\Attribute\AsDomainEvent;

#[AsDomainEvent(routingKey: '$.system.user.deleted')]
class UserDeleted extends AbstractDomainEvent
{
    public function __construct(
        public ?string $userId = null,
    ) {
        parent::__construct();
    }
}
