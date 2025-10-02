<?php

namespace Marvin\System\Application\Command;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandInterface;
use Marvin\Shared\Domain\ValueObject\Description;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Reference;
use Marvin\System\Domain\ValueObject\Metadata;
use Marvin\System\Domain\ValueObject\Version;

final readonly class CreatePlugin implements SyncCommandInterface
{
    public function __construct(
        public Label $label,
        public Reference $reference,
        public Version $version,
        public Reference $statusReference,
        public Metadata $metadata = new Metadata(),
        public ?Description $description = null,
    ) {
    }
}
