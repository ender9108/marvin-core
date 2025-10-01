<?php

namespace Marvin\System\Domain\Repository;

use Marvin\System\Domain\Model\Docker;
use Marvin\System\Domain\ValueObject\Identity\DockerId;

interface DockerRepositoryInterface
{
    public function save(Docker $docker, bool $flush = true): void;

    public function remove(Docker $docker, bool $flush = true): void;

    public function byId(DockerId $dockerId): Docker;
}
