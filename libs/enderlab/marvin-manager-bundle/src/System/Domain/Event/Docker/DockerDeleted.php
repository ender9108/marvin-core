<?php
namespace EnderLab\MarvinManagerBundle\System\Domain\Event\Docker;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;
use EnderLab\DddCqrsBundle\Domain\Event\Attribute\AsDomainEvent;

#[AsDomainEvent(routingKey: '$.system.docker.deleted')]
class DockerDeleted extends AbstractDomainEvent
{
    public function __construct(
        public ?string $id = null,
    ) {
        parent::__construct();
    }
}
