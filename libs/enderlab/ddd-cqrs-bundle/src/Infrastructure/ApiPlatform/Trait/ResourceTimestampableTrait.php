<?php

namespace EnderLab\DddCqrsBundle\Infrastructure\ApiPlatform\Trait;

use ApiPlatform\Metadata\ApiProperty;
use DateTimeInterface;

trait ResourceTimestampableTrait
{
    #[ApiProperty(readable: true, writable: false)]
    public ?DateTimeInterface $createdAt = null;

    #[ApiProperty(readable: true, writable: false)]
    public ?DateTimeInterface $updatedAt = null;
}
