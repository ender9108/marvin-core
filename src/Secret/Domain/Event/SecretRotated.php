<?php

namespace Marvin\Secret\Domain\Event;

use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

final readonly class SecretRotated extends AbstractDomainEvent
{
    public function __construct(
        public string $secretId,
        public string $key,
        public string $scope,
        public string $category,
        public bool $automatic,
        public ?string $previousValueHash, // Hash de l'ancienne valeur (pour traçabilité)
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
            'automatic' => $this->automatic,
            'previous_value_hash' => $this->previousValueHash,
            'occurred_on' => $this->occurredOn->format('c'),
        ];
    }
}
