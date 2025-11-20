<?php

/**
 * Marvin Core - DDD-based home automation system
 *
 * @package   Marvin\Core
 * @author    Alexandre Berthelot <alexandreberthelot9108@gmail.com>
 * @copyright 2024-present Alexandre Berthelot
 * @license   AGPL-3.0 License
 * @link      https://github.com/ender9108/marvin-core
 */

declare(strict_types=1);

namespace Marvin\Tests\Security\Functional\Api;

use Marvin\Security\Domain\ValueObject\Role;
use Marvin\Security\Domain\ValueObject\UserStatus;
use Marvin\Security\Domain\ValueObject\UserType;
use Marvin\Security\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Factory\UserFactory;
use Marvin\Security\Infrastructure\Framework\Symfony\Security\SecurityUser;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Browser\HttpOptions;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\ResetDatabase;

final class UserCrudApiTest extends KernelTestCase
{
    use HasBrowser;
    use ResetDatabase;

    private function createUser(array $overrides = []): \Zenstruck\Foundry\Persistence\Proxy
    {
        static $counter = 0;

        $defaults = [
            'email' => 'user' . (++$counter) . '@test.local',
            'firstname' => 'Test',
            'lastname' => 'User',
            'password' => 'Test123456789',
            'roles' => [Role::USER],
            'status' => UserStatus::ENABLED,
            'locale' => 'en',
            'theme' => 'light',
            'timezone' => 'UTC',
            'type' => UserType::APP,
        ];

        return UserFactory::createOne(array_merge($defaults, $overrides));
    }

    public function testAdminCanCreateUser(): void
    {
        $admin = $this->createUser([
            'email' => 'admin@test.com',
            'roles' => [Role::ADMIN],
        ]);

        $this->browser()
            ->actingAs(SecurityUser::create($admin->_real()))
            ->post('/api/security/users', HttpOptions::json([
                'email' => 'newuser@test.com',
                'firstname' => 'New',
                'lastname' => 'User',
                'roles' => ['ROLE_USER'],
                'locale' => 'en',
                'theme' => 'light',
                'timezone' => 'UTC',
                'password' => 'SecurePassword123!',
            ]))
            ->assertStatus(201)
            ->assertJsonMatches('email', 'newuser@test.com')
            ->assertJsonMatches('firstname', 'New')
            ->assertJsonMatches('lastname', 'User');
    }

    public function testAdminCanListUsers(): void
    {
        $admin = $this->createUser([
            'email' => 'admin@test.com',
            'roles' => [Role::ADMIN],
        ]);

        UserFactory::createMany(5, [
            'firstname' => 'Test',
            'lastname' => 'User',
            'password' => 'Test123456789',
            'status' => UserStatus::ENABLED,
            'roles' => [Role::USER],
            'locale' => 'en',
            'theme' => 'light',
            'timezone' => 'UTC',
            'type' => UserType::APP,
        ]);

        $this->browser()
            ->actingAs(SecurityUser::create($admin->_real()))
            ->get('/api/security/users')
            ->assertSuccessful()
            ->assertJsonMatches('length("hydra:member")', 6); // 5 + admin
    }

    public function testAdminCanGetUserById(): void
    {
        $admin = $this->createUser([
            'roles' => [Role::ADMIN],
        ]);

        $user = $this->createUser([
            'email' => 'targetuser@test.com',
            'firstname' => 'Target',
            'lastname' => 'User',
        ]);

        $this->browser()
            ->actingAs(SecurityUser::create($admin->_real()))
            ->get('/api/security/users/' . $user->id->toString())
            ->assertSuccessful()
            ->assertJsonMatches('email', 'targetuser@test.com')
            ->assertJsonMatches('firstname', 'Target');
    }

    public function testUserCanGetTheirOwnProfile(): void
    {
        $user = $this->createUser([
            'email' => 'user@test.com',
            'firstname' => 'John',
            'lastname' => 'Doe',
        ]);

        $this->browser()
            ->actingAs(SecurityUser::create($user->_real()))
            ->get('/api/security/users/' . $user->id->toString())
            ->assertSuccessful()
            ->assertJsonMatches('email', 'user@test.com')
            ->assertJsonMatches('firstname', 'John');
    }

    public function testAdminCanDeleteUser(): void
    {
        $admin = $this->createUser([
            'roles' => [Role::ADMIN],
        ]);

        $userToDelete = $this->createUser([
            'email' => 'todelete@test.com',
        ]);

        $userId = $userToDelete->id->toString();

        $this->browser()
            ->actingAs(SecurityUser::create($admin->_real()))
            ->delete('/api/security/users/' . $userId)
            ->assertStatus(204);

        // Verify user is deleted
        $this->browser()
            ->actingAs(SecurityUser::create($admin->_real()))
            ->get('/api/security/users/' . $userId)
            ->assertStatus(404);
    }

    public function testUnauthenticatedUserCannotAccessUserList(): void
    {
        $this->browser()
            ->get('/api/security/users')
            ->assertStatus(401);
    }

    public function testRegularUserCannotCreateUser(): void
    {
        $user = $this->createUser([
            'email' => 'user@test.com',
        ]);

        $this->browser()
            ->actingAs(SecurityUser::create($user->_real()))
            ->post('/api/security/users', HttpOptions::json([
                'email' => 'another@test.com',
                'firstname' => 'Test',
                'lastname' => 'User',
                'password' => 'Test123!',
            ]))
            ->assertStatus(403);
    }

    public function testCreateUserWithInvalidDataReturnsValidationError(): void
    {
        $admin = $this->createUser([
            'roles' => [Role::ADMIN],
        ]);

        $this->browser()
            ->actingAs(SecurityUser::create($admin->_real()))
            ->post('/api/security/users', HttpOptions::json([
                'email' => 'not-an-email',
                'firstname' => '',
                'password' => '123', // Too short
            ]))
            ->assertStatus(422); // Unprocessable Entity
    }
}
