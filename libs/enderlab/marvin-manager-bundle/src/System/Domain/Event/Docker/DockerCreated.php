<?php
namespace EnderLab\MarvinManagerBundle\System\Domain\Event\Docker;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;
use EnderLab\DddCqrsBundle\Domain\Event\DomainEventInterface;

final readonly class DockerCreated extends AbstractDomainEvent implements DomainEventInterface
{
    public function __construct(
        public ?string $id = null,
    ) {
        parent::__construct();
    }

    public static function getRoutingKey(): string
    {
        return '$.system.docker.created';
    }
}
