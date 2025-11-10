<?php

declare(strict_types=1);

namespace Marvin\Protocol\Application\Service\Acl\Dto;

final readonly class AdapterDto
{
    public function __construct(
        public string $name,
        public string $className,
        public string $protocolType,
        public string $defaultExecutionMode,
        public array $supportedExecutionModes,
        public string $description,
    ) {
    }
}
