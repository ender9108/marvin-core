<?php

namespace Marvin\Shared\Application\Acl;

interface SecretQueryServiceInterface
{
    public function getSecretValue(string $key): string;

    public function getSecretInfo(string $key): SecretInfo;

    public function exists(string $key): bool;

    /**
     * @param string[] $keys
     * @return array<string, string>
     */
    public function getSecretValues(array $keys): array;

    /**
     * @return array<string, string>
     */
    public function getSecretsByCategory(string $category): array;
}
