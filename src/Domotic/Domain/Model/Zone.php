<?php

namespace Marvin\Domotic\Domain\Model;

use DateTimeImmutable;
use Marvin\Domotic\Domain\ValueObject\Area;
use Marvin\Domotic\Domain\ValueObject\Identity\ZoneId;
use Marvin\Shared\Domain\ValueObject\CreatedAt;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\UpdatedAt;

final class Zone
{
    public readonly ZoneId $id;

    public function __construct(
        private(set) Label $label,
        private(set) Area $area,
        private(set) ?Zone $parentZone = null,
        private(set) ?UpdatedAt $updatedAt = null,
        public readonly CreatedAt $createdAt = new CreatedAt(new DateTimeImmutable())
    ) {
        $this->id = new ZoneId();
    }
}
