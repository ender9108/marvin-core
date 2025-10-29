<?php

namespace Marvin\PluginManager\Domain\Repository;

use Marvin\PluginManager\Domain\Model\Plugin;
use Marvin\PluginManager\Domain\ValueObject\PluginSlug;
use Marvin\Shared\Domain\ValueObject\Identity\PluginId;

interface PluginRepositoryInterface
{
    public function save(Plugin $plugin): void;
    public function delete(Plugin $plugin): void;
    public function findById(PluginId $id): ?Plugin;
    public function findBySlug(PluginSlug $slug): ?Plugin;
    public function findByClass(string $class): ?Plugin;
    public function findByPackage(string $package): ?Plugin;
    public function findAll(): array;
    public function findEnabled(): array;
    public function exists(PluginId $id): bool;
}
