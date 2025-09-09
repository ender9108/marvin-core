<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Domotic\Infrastructure\Foundry\Story\DomoticStory;
use App\System\Infrastructure\Foundry\Story\SecurityStory;

final class SystemApiTest extends ApiTestCase
{
    private static ?string $jwt = null;

    protected function setUp(): void
    {
        // Ensure initial data exists for tests (users, statuses, types, etc.)
        SecurityStory::load();
        DomoticStory::load();

        if (null === self::$jwt) {
            self::$jwt = $this->authenticateAsAdmin();
        }
    }

    private function authenticateAsAdmin(): string
    {
        $client = static::createClient(defaultOptions: [
            'headers' => [
                'accept' => 'application/json',
                'content-type' => 'application/json',
            ],
        ]);

        $response = $client->request('POST', '/api/login_check', [
            'json' => [
                'username' => 'administrator@marvin.test',
                'password' => 'Test123456789',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $data = $response->toArray(false);
        $this->assertArrayHasKey('token', $data, 'JWT token not found in login response.');

        return $data['token'];
    }

    private function authClient(): \ApiPlatform\Symfony\Bundle\Test\Client
    {
        return static::createClient(defaultOptions: [
            'headers' => [
                'accept' => 'application/json',
                'authorization' => 'Bearer '.self::$jwt,
            ],
        ]);
    }

    public function test_login_check_returns_token(): void
    {
        $client = static::createClient(defaultOptions: [
            'headers' => [
                'accept' => 'application/json',
                'content-type' => 'application/json',
            ],
        ]);

        $response = $client->request('POST', '/api/login_check', [
            'json' => [
                'username' => 'administrator@marvin.test',
                'password' => 'Test123456789',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $data = $response->toArray(false);
        $this->assertArrayHasKey('token', $data);
    }

    public function test_get_users_collection_as_admin(): void
    {
        $client = $this->authClient();
        $client->request('GET', '/api/system/users');
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
    }

    public function test_get_me_endpoint_as_admin(): void
    {
        $client = $this->authClient();
        $client->request('GET', '/api/system/users/me');
        $this->assertResponseIsSuccessful();
    }

    public function test_get_plugins_collection_as_admin(): void
    {
        $client = $this->authClient();
        $client->request('GET', '/api/system/plugins');
        $this->assertResponseIsSuccessful();
    }

    public function test_get_user_statuses_and_user_types_collections_as_admin(): void
    {
        $client = $this->authClient();
        $client->request('GET', '/api/system/user_statuses');
        $this->assertResponseIsSuccessful();

        $client->request('GET', '/api/system/user_types');
        $this->assertResponseIsSuccessful();
    }

    public function test_protected_endpoints_require_authentication(): void
    {
        $client = static::createClient(defaultOptions: [
            'headers' => [
                'accept' => 'application/json',
            ],
        ]);

        $client->request('GET', '/api/system/users');
        $this->assertResponseStatusCodeSame(401);
    }

    public function test_get_plugin_statuses_collection_as_admin_and_get_item(): void
    {
        $client = $this->authClient();
        $response = $client->request('GET', '/api/system/plugin_statuses');
        $this->assertResponseIsSuccessful();
        $statuses = $response->toArray(false);

        // Find first item id regardless of format (array of objects with id field)
        $this->assertIsArray($statuses);
        $this->assertNotEmpty($statuses);
        $first = $statuses[0];
        $this->assertArrayHasKey('id', $first);

        $client->request('GET', '/api/system/plugin_statuses/'.$first['id']);
        $this->assertResponseIsSuccessful();
    }
}
