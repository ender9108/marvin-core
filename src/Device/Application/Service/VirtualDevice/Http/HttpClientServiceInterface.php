<?php

namespace Marvin\Device\Application\Service\VirtualDevice\Http;

interface HttpClientServiceInterface
{
    public function request(
        string $method,
        string $url,
        array $options = []
    ): HttpResponse;
}
