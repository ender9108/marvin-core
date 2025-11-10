<?php

declare(strict_types=1);

namespace Marvin\Protocol\Application\Service\Acl\Dto;

final readonly class RestCredentialsDto
{
    public function __construct(
        public string $baseUri,
        public ?string $authType = null,
        public ?string $username = null,
        public ?string $password = null,
        public ?string $bearerToken = null,
        public float $timeout = 5.0,
        public array $headers = [],
    ) {
    }
}
