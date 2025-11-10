<?php

declare(strict_types=1);

namespace Marvin\Protocol\Infrastructure\Protocol;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * REST Protocol Implementation using Symfony HttpClient
 * Supports GET, POST, PUT, DELETE with various authentication methods
 */
final readonly class RestProtocol
{
    public function __construct(
        private HttpClientInterface $httpClient,
    ) {
    }

    /**
     * Send GET request
     *
     * @param string $url Full URL or path (if baseUri is configured)
     * @param array $options Request options (headers, query, auth, etc.)
     * @throws TransportExceptionInterface
     */
    public function get(string $url, array $options = []): ResponseInterface
    {
        return $this->httpClient->request('GET', $url, $options);
    }

    /**
     * Send POST request
     *
     * @param string $url Full URL or path
     * @param array|string $data Request body (will be JSON encoded if array)
     * @param array $options Request options
     * @throws TransportExceptionInterface
     */
    public function post(string $url, array|string $data = [], array $options = []): ResponseInterface
    {
        if (is_array($data)) {
            $options['json'] = $data;
        } else {
            $options['body'] = $data;
        }

        return $this->httpClient->request('POST', $url, $options);
    }

    /**
     * Send PUT request
     *
     * @param string $url Full URL or path
     * @param array|string $data Request body
     * @param array $options Request options
     * @throws TransportExceptionInterface
     */
    public function put(string $url, array|string $data = [], array $options = []): ResponseInterface
    {
        if (is_array($data)) {
            $options['json'] = $data;
        } else {
            $options['body'] = $data;
        }

        return $this->httpClient->request('PUT', $url, $options);
    }

    /**
     * Send PATCH request
     *
     * @param string $url Full URL or path
     * @param array|string $data Request body
     * @param array $options Request options
     * @throws TransportExceptionInterface
     */
    public function patch(string $url, array|string $data = [], array $options = []): ResponseInterface
    {
        if (is_array($data)) {
            $options['json'] = $data;
        } else {
            $options['body'] = $data;
        }

        return $this->httpClient->request('PATCH', $url, $options);
    }

    /**
     * Send DELETE request
     *
     * @param string $url Full URL or path
     * @param array $options Request options
     * @throws TransportExceptionInterface
     */
    public function delete(string $url, array $options = []): ResponseInterface
    {
        return $this->httpClient->request('DELETE', $url, $options);
    }

    /**
     * Send generic request
     *
     * @param string $method HTTP method
     * @param string $url Full URL or path
     * @param array $options Request options
     * @throws TransportExceptionInterface
     */
    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        return $this->httpClient->request($method, $url, $options);
    }

    /**
     * Create scoped client with base URI and default options
     *
     * @param string $baseUri Base URI for all requests
     * @param array $defaultOptions Default options for all requests
     */
    public function createScopedClient(string $baseUri, array $defaultOptions = []): HttpClientInterface
    {
        $options = array_merge(['base_uri' => $baseUri], $defaultOptions);

        return $this->httpClient->withOptions($options);
    }

    /**
     * Build authentication options for Basic Auth
     *
     * @param string $username Username
     * @param string $password Password
     */
    public static function buildBasicAuth(string $username, string $password): array
    {
        return [
            'auth_basic' => [$username, $password],
        ];
    }

    /**
     * Build authentication options for Digest Auth
     *
     * @param string $username Username
     * @param string $password Password
     */
    public static function buildDigestAuth(string $username, string $password): array
    {
        return [
            'auth_digest' => [$username, $password],
        ];
    }

    /**
     * Build authentication options for Bearer Token
     *
     * @param string $token Bearer token
     */
    public static function buildBearerAuth(string $token): array
    {
        return [
            'auth_bearer' => $token,
        ];
    }

    /**
     * Build headers options
     *
     * @param array $headers Custom headers
     */
    public static function buildHeaders(array $headers): array
    {
        return [
            'headers' => $headers,
        ];
    }
}
