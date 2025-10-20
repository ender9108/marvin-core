<?php

namespace Marvin\Device\Domain\Model;

use EnderLab\DddCqrsBundle\Domain\Model\AggregateRoot;
use Marvin\Device\Domain\ValueObject\Identity\DeviceId;
use Marvin\Shared\Domain\ValueObject\Label;


class Device extends AggregateRoot
{
    public readonly DeviceId $id;

    public function __construct(
        private(set) Label $label,
    ) {
        $this->id = new DeviceId();
    }
}
