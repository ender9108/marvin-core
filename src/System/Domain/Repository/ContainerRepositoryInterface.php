<?php

namespace Marvin\System\Domain\Repository;

use Marvin\System\Domain\Model\Container;
use Marvin\System\Domain\ValueObject\Identity\ContainerId;

interface ContainerRepositoryInterface
{
    public function save(Container $model, bool $flush = true): void;

    public function remove(Container $model, bool $flush = true): void;

    public function byId(ContainerId $id): Container;
}
