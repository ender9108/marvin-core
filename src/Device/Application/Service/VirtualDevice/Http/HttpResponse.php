<?php

namespace Marvin\Device\Application\Service\VirtualDevice\Http;

final readonly class HttpResponse
{
    public function __construct(
        public int $statusCode,
        public string $body,
        public array $headers,
        public float $duration
    ) {
    }

    public function isSuccess(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    public function toArray(): array
    {
        return [
            'status_code' => $this->statusCode,
            'body' => $this->body,
            'headers' => $this->headers,
            'duration' => $this->duration,
            'success' => $this->isSuccess(),
        ];
    }
}
