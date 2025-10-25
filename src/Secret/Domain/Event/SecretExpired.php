<?php

namespace Marvin\Secret\Domain\Event;

use DateTimeImmutable;
use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

final readonly class SecretExpired extends AbstractDomainEvent
{
    public function __construct(
        public string $secretId,
        public string $key,
        public string $scope,
        public string $category,
        public DateTimeImmutable $expiredAt,
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
            'expired_at' => $this->expiredAt->format('c'),
            'occurred_on' => $this->occurredOn->format('c'),
        ];
    }
}
