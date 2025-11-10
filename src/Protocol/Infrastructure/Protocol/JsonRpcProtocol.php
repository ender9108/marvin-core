<?php

declare(strict_types=1);

namespace Marvin\Protocol\Infrastructure\Protocol;

use RuntimeException;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * JSON-RPC 2.0 Protocol Implementation using Symfony HttpClient
 * Supports request/response with correlation via id field
 */
final class JsonRpcProtocol
{
    private int $requestId;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
    ) {
        $this->requestId = 1;
    }

    /**
     * Send JSON-RPC 2.0 request
     *
     * @param string $url JSON-RPC endpoint URL
     * @param string $method RPC method name
     * @param array $params Method parameters
     * @param int|string|null $id Request ID (auto-generated if null)
     * @param array $options Additional HTTP options
     * @return array Response data
     * @throws RuntimeException On JSON-RPC error
     * @throws TransportExceptionInterface
     * @throws DecodingExceptionInterface
     */
    public function call(
        string $url,
        string $method,
        array $params = [],
        int|string|null $id = null,
        array $options = []
    ): array {
        $requestId = $id ?? $this->requestId++;

        $request = [
            'jsonrpc' => '2.0',
            'method' => $method,
            'params' => $params,
            'id' => $requestId,
        ];

        $options['json'] = $request;
        $options['headers'] = array_merge(
            $options['headers'] ?? [],
            ['Content-Type' => 'application/json']
        );

        $response = $this->httpClient->request('POST', $url, $options);

        $data = $response->toArray();

        // Check for JSON-RPC error
        if (isset($data['error'])) {
            throw new RuntimeException(
                sprintf(
                    'JSON-RPC Error [%d]: %s',
                    $data['error']['code'] ?? 0,
                    $data['error']['message'] ?? 'Unknown error'
                )
            );
        }

        return $data;
    }

    /**
     * Send notification (request without expecting response)
     *
     * @param string $url JSON-RPC endpoint URL
     * @param string $method RPC method name
     * @param array $params Method parameters
     * @param array $options Additional HTTP options
     * @throws TransportExceptionInterface
     */
    public function notify(
        string $url,
        string $method,
        array $params = [],
        array $options = []
    ): void {
        $request = [
            'jsonrpc' => '2.0',
            'method' => $method,
            'params' => $params,
        ];

        $options['json'] = $request;
        $options['headers'] = array_merge(
            $options['headers'] ?? [],
            ['Content-Type' => 'application/json']
        );

        $this->httpClient->request('POST', $url, $options);
    }

    /**
     * Send batch request
     *
     * @param string $url JSON-RPC endpoint URL
     * @param array $calls Array of [method, params, id] arrays
     * @param array $options Additional HTTP options
     * @return array Array of responses
     * @throws TransportExceptionInterface
     */
    public function batch(string $url, array $calls, array $options = []): array
    {
        $requests = [];

        foreach ($calls as $call) {
            $method = $call['method'] ?? $call[0];
            $params = $call['params'] ?? $call[1] ?? [];
            $id = $call['id'] ?? $call[2] ?? $this->requestId++;

            $requests[] = [
                'jsonrpc' => '2.0',
                'method' => $method,
                'params' => $params,
                'id' => $id,
            ];
        }

        $options['json'] = $requests;
        $options['headers'] = array_merge(
            $options['headers'] ?? [],
            ['Content-Type' => 'application/json']
        );

        $response = $this->httpClient->request('POST', $url, $options);

        return $response->toArray();
    }

    /**
     * Create scoped client with base URI
     *
     * @param string $baseUri Base URI for all requests
     * @param array $defaultOptions Default options
     */
    public function createScopedClient(string $baseUri, array $defaultOptions = []): HttpClientInterface
    {
        $options = array_merge(['base_uri' => $baseUri], $defaultOptions);

        return $this->httpClient->withOptions($options);
    }

    /**
     * Parse JSON-RPC response
     *
     * @param array $response Raw response data
     * @return mixed Result data
     * @throws RuntimeException On error
     */
    public static function parseResponse(array $response): mixed
    {
        if (isset($response['error'])) {
            throw new RuntimeException(
                sprintf(
                    'JSON-RPC Error [%d]: %s',
                    $response['error']['code'] ?? 0,
                    $response['error']['message'] ?? 'Unknown error'
                )
            );
        }

        return $response['result'] ?? null;
    }
}
