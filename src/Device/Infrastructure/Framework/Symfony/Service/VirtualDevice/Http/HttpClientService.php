<?php

namespace Marvin\Device\Infrastructure\Framework\Symfony\Service\VirtualDevice\Http;

use Marvin\Device\Application\Service\VirtualDevice\Http\HttpClientServiceInterface;
use Marvin\Device\Application\Service\VirtualDevice\Http\HttpResponse;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class HttpClientService implements HttpClientServiceInterface
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private LoggerInterface $logger
    ) {
    }

    public function request(string $method, string $url, array $options = []): HttpResponse
    {
        $this->logger->debug('HTTP request for virtual device', [
            'method' => $method,
            'url' => $url,
        ]);

        $startTime = microtime(true);

        try {
            $response = $this->httpClient->request($method, $url, $options);

            $statusCode = $response->getStatusCode();
            $body = $response->getContent(false); // false = ne pas throw sur erreur HTTP
            $headers = $response->getHeaders(false);

            $duration = microtime(true) - $startTime;

            $this->logger->info('HTTP request completed', [
                'method' => $method,
                'url' => $url,
                'status' => $statusCode,
                'duration' => round($duration, 3),
            ]);

            return new HttpResponse(
                statusCode: $statusCode,
                body: $body,
                headers: $headers,
                duration: $duration
            );
        } catch (\Throwable $e) {
            $duration = microtime(true) - $startTime;

            $this->logger->error('HTTP request failed', [
                'method' => $method,
                'url' => $url,
                'error' => $e->getMessage(),
                'duration' => round($duration, 3),
            ]);

            throw new \RuntimeException("HTTP request failed: {$e->getMessage()}", 0, $e);
        }
    }
}
