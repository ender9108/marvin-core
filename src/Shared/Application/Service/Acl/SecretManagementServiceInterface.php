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

namespace Marvin\Shared\Application\Service\Acl;

interface SecretManagementServiceInterface
{
    public function createSecret(
        string $key,
        string $value,
        string $category,
        string $scope = 'device',
        bool $managed = false,
        int $rotationIntervalDays = 0,
        array $metadata = [],
    ): void;

    public function updateSecret(string $key, string $value): void;

    public function ensureSecret(
        string $key,
        string $value,
        string $category,
        string $scope = 'device',
        bool $managed = false,
        int $rotationIntervalDays = 0,
        array $metadata = [],
    ): bool;

    public function rotateSecret(string $key, ?string $newValue = null): void;

    public function deleteSecret(string $key): void;

    public function updateRotationPolicy(
        string $key,
        bool $managed,
        int $rotationIntervalDays,
    ): void;
}
