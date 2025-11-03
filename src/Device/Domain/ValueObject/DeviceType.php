<?php

namespace Marvin\Device\Domain\ValueObject;

use EnderLab\ToolsBundle\Service\EnumToArrayTrait;

enum DeviceType: string
{
    use EnumToArrayTrait;

    case PHYSICAL = 'physical';
    case VIRTUAL = 'virtual';
    case COMPOSITE = 'composite';

    public function isPhysical(): bool
    {
        return $this === self::PHYSICAL;
    }

    public function isVirtual(): bool
    {
        return $this === self::VIRTUAL;
    }

    public function isComposite(): bool
    {
        return $this === self::COMPOSITE;
    }
}
