<?php

declare(strict_types=1);

namespace Marvin\Protocol\Application\Service\Acl\Dto;

final readonly class WebSocketCredentialsDto
{
    public function __construct(
        public string $url,
        public bool $ssl = false,
        public float $timeout = 5.0,
        public array $headers = [],
        public ?string $authType = null,
        public ?string $username = null,
        public ?string $password = null,
    ) {
    }
}
