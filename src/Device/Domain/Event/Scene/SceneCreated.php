<?php

namespace Marvin\Device\Domain\Event\Scene;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

final readonly class SceneCreated extends AbstractDomainEvent
{
    public function __construct(
        public string $sceneId,
        public string $name,
        public array $deviceIds,
        public bool $hasNativeSupport
    ) {
        parent::__construct();
    }
}
