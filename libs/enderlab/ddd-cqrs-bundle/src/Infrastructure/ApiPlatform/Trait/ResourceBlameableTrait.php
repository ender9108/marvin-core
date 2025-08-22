<?php

namespace EnderLab\DddCqrsBundle\Infrastructure\ApiPlatform\Trait;

use ApiPlatform\Metadata\ApiProperty;

trait ResourceBlameableTrait
{
    #[ApiProperty(readable: true, writable: false)]
    public ?string $createdBy = null;

    #[ApiProperty(readable: true, writable: false)]
    public ?string $updatedBy = null;
}
