<?php
namespace Marvin\System\Domain\Model;

use DateTimeImmutable;
use Marvin\Shared\Domain\ValueObject\CreatedAt;
use Marvin\Shared\Domain\ValueObject\Reference;
use Marvin\Shared\Domain\ValueObject\UpdatedAt;
use Marvin\System\Domain\ValueObject\Command;
use Marvin\System\Domain\ValueObject\Identity\DockerCommandId;

class DockerCommand
{
    public readonly DockerCommandId $id;

    public function __construct(
        private(set) Reference $reference,
        private(set) Command $command,
        private(set) UpdatedAt $updatedAt,
        public ?Docker $docker = null,
        private(set) CreatedAt $createdAt = new CreatedAt(new DateTimeImmutable())
    ) {
    }
}
