<?php

namespace App\System\Domain\Repository;

use App\System\Domain\Model\PluginStatus;
use EnderLab\DddCqrsBundle\Domain\Repository\RepositoryInterface;

interface PluginStatusRepositoryInterface extends RepositoryInterface
{
    public function add(PluginStatus $pluginStatus): void;

    public function remove(PluginStatus $pluginStatus): void;

    public function byId(int $id): ?PluginStatus;
    public function byReference(string $reference): ?PluginStatus;
}
