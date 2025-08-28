<?php

namespace App\System\Domain\Event\User;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;
use EnderLab\DddCqrsBundle\Domain\Event\Attribute\AsDomainEvent;

#[AsDomainEvent(routingKey: '$.system.user.updated')]
class UserUpdated extends AbstractDomainEvent
{
    public function __construct(
        public ?string $id = null,
        public array $changes = [],
    ) {
        parent::__construct();
    }
}
