<?php
namespace Marvin\System\Domain\Model;

use DateTimeImmutable;
use Marvin\Shared\Domain\ValueObject\CreatedAt;
use Marvin\Shared\Domain\ValueObject\Reference;
use Marvin\Shared\Domain\ValueObject\UpdatedAt;
use Marvin\System\Domain\ValueObject\Command;
use Marvin\System\Domain\ValueObject\Identity\DockerCommandId;

final readonly class DockerCommand
{
    public DockerCommandId $id;

    public function __construct(
        private(set) Reference $reference,
        private(set) Command $command,
        private(set) ?Docker $docker = null,
        private(set) ?UpdatedAt $updatedAt = null,
        private(set) CreatedAt $createdAt = new CreatedAt(new DateTimeImmutable())
    ) {
    }
}
