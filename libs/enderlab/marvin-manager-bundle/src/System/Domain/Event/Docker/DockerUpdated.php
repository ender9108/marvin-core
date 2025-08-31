<?php
namespace EnderLab\MarvinManagerBundle\System\Domain\Event\Docker;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;
use EnderLab\DddCqrsBundle\Domain\Event\Attribute\AsDomainEvent;

#[AsDomainEvent(routingKey: '$.system.docker.updated')]
class DockerUpdated extends AbstractDomainEvent
{
    public function __construct(
        public ?string $id = null,
    ) {
        parent::__construct();
    }
}
