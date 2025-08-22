<?php

namespace App\System\Domain\Repository;

use App\System\Domain\Model\Plugin;
use EnderLab\DddCqrsBundle\Domain\Repository\RepositoryInterface;

interface PluginRepositoryInterface extends RepositoryInterface
{
    public function add(Plugin $plugin): void;

    public function remove(Plugin $plugin): void;

    public function byId(int $id): ?Plugin;

    public function isEnabled(string $reference): bool;
    public function getByReference(string $reference): ?Plugin;
}
