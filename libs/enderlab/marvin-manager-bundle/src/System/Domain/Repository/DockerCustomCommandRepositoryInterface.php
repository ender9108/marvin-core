<?php
namespace EnderLab\MarvinManagerBundle\System\Domain\Repository;

use EnderLab\DddCqrsBundle\Domain\Repository\RepositoryInterface;
use EnderLab\MarvinManagerBundle\System\Domain\Model\DockerCustomCommand;
use EnderLab\MarvinManagerBundle\System\Domain\ValueObject\Identity\DockerCustomCommandId;

interface DockerCustomCommandRepositoryInterface extends RepositoryInterface
{
    public function add(DockerCustomCommand $command): void;

    public function remove(DockerCustomCommand $command): void;

    public function byId(DockerCustomCommandId $id): DockerCustomCommand;
}
