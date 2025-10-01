<?php

namespace Marvin\System\Domain\Repository;

use Marvin\System\Domain\Model\DockerCommand;
use Marvin\System\Domain\ValueObject\Identity\DockerCommandId;

interface DockerCommandRepositoryInterface
{
    public function save(DockerCommand $dockerCommand, bool $flush = true): void;

    public function remove(DockerCommand $dockerCommand, bool $flush = true): void;

    public function byId(DockerCommandId $dockerCommandId): DockerCommand;
}
