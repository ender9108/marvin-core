<?php

namespace Marvin\Device\Domain\Event\Scene;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

final readonly class SceneActivated extends AbstractDomainEvent
{
    public function __construct(
        public string $sceneId,
        public string $label,
        public bool $usedNative
    ) {
        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'scene_id' => $this->sceneId,
            'label' => $this->label,
            'used_native' => $this->usedNative,
        ];
    }
}
