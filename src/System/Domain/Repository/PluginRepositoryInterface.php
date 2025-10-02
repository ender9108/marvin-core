<?php

namespace Marvin\System\Domain\Repository;

use Marvin\Shared\Domain\ValueObject\Reference;
use Marvin\System\Domain\Model\Plugin;
use Marvin\System\Domain\ValueObject\Identity\PluginId;

interface PluginRepositoryInterface
{
    public function save(Plugin $model, bool $flush = true): void;

    public function remove(Plugin $model, bool $flush = true): void;

    public function byId(PluginId $id): Plugin;

    public function exists(Reference $reference): bool;

    public function getByReference(Reference $reference): ?Plugin;
}
