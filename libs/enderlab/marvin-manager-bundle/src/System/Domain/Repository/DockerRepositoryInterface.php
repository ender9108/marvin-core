<?php
namespace EnderLab\MarvinManagerBundle\System\Domain\Repository;

use EnderLab\DddCqrsBundle\Domain\Repository\RepositoryInterface;
use EnderLab\MarvinManagerBundle\System\Domain\Model\Docker;

interface DockerRepositoryInterface extends RepositoryInterface
{
    public function add(Docker $docker): void;

    public function remove(Docker $docker): void;

    public function byId(string $id): ?Docker;
}
