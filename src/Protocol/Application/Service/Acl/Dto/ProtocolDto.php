<?php

declare(strict_types=1);

namespace Marvin\Protocol\Application\Service\Acl\Dto;

use DateTimeInterface;

final readonly class ProtocolDto
{
    public function __construct(
        public string $id,
        public string $type,
        public string $name,
        public array $configuration,
        public string $status,
        public string $preferredExecutionMode,
        public DateTimeInterface $createdAt,
        public ?DateTimeInterface $updatedAt = null,
    ) {
    }
}
