<?php

declare(strict_types=1);

namespace Marvin\Protocol\Application\Service\Acl\Dto;

final readonly class MqttCredentialsDto
{
    public function __construct(
        public string $host,
        public int $port,
        public ?string $username = null,
        public ?string $password = null,
        public int $protocolLevel = 5,
        public int $qos = 1,
        public bool $retain = false,
    ) {
    }
}
