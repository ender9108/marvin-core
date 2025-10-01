<?php

namespace Marvin\System\Domain\Repository;

use Marvin\System\Domain\Model\PluginStatus;
use Marvin\System\Domain\ValueObject\Identity\PluginStatusId;

interface PluginStatusRepositoryInterface
{
    public function save(PluginStatus $model, bool $flush = true): void;

    public function remove(PluginStatus $model, bool $flush = true): void;

    public function byId(PluginStatusId $id): PluginStatus;
}
