<?php

declare(strict_types=1);

namespace Marvin\Protocol\Application\Service\Acl\Dto;

final readonly class DeviceDto
{
    public function __construct(
        public string $id,
        public string $label,
        public string $protocol,
        public string $nativeId,
        public ?string $mqttTopic = null,
        public ?string $restUrl = null,
        public ?array $metadata = null,
    ) {
    }
}
