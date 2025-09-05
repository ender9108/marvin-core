<?php
namespace EnderLab\MarvinManagerBundle\System\Domain\Repository;

use EnderLab\DddCqrsBundle\Domain\Repository\RepositoryInterface;
use EnderLab\MarvinManagerBundle\System\Domain\Model\Docker;
use EnderLab\MarvinManagerBundle\System\Domain\ValueObject\Identity\DockerId;

interface DockerRepositoryInterface extends RepositoryInterface
{
    public function add(Docker $docker, bool $flush = true): void;

    public function remove(Docker $docker, bool $flush = true): void;

    public function byId(DockerId $id): Docker;
}
