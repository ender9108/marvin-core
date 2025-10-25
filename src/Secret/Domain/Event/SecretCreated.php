<?php

namespace Marvin\Secret\Domain\Event;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

final readonly class SecretCreated extends AbstractDomainEvent
{
    public function __construct(
        public string $secretId,
        public string $key,
        public string $scope,
        public string $category,
        public bool $autoRotate,
        public int $rotationIntervalDays,
    ) {
        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'secret_id' => $this->secretId,
            'key' => $this->key,
            'scope' => $this->scope,
            'category' => $this->category,
            'auto_rotate' => $this->autoRotate,
            'rotation_interval_days' => $this->rotationIntervalDays,
            'occurred_on' => $this->occurredOn->format('c'),
        ];
    }
}
