<?php

namespace Marvin\Shared\Application\Acl;

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
