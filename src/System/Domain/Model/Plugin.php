<?php

namespace Marvin\System\Domain\Model;

use EnderLab\DddCqrsBundle\Domain\Model\AggregateRoot;
use Marvin\Shared\Domain\ValueObject\Description;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Metadata;
use Marvin\Shared\Domain\ValueObject\Reference;
use Marvin\System\Domain\ValueObject\Identity\PluginId;
use Marvin\System\Domain\ValueObject\PluginStatus;
use Marvin\System\Domain\ValueObject\Version;

class Plugin extends AggregateRoot
{
    public PluginId $id;

    public function __construct(
        private(set) Label $label,
        private(set) Reference $reference,
        private(set) Version $version,
        private(set) PluginStatus $status,
        private(set) Metadata $metadata = new Metadata(),
        private(set) ?Description $description = null,
    ) {
        $this->id = new PluginId();
    }
}
