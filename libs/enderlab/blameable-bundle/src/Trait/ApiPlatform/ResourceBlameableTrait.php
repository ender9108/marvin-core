<?php

namespace EnderLab\BlameableBundle\Trait\ApiPlatform;

use ApiPlatform\Metadata\ApiProperty;

trait ResourceBlameableTrait
{
    #[ApiProperty(
        readable: true,
        writable: false,
        openapiContext: [
            'type' => 'string',
            'example' => '/api/system/users/95348868-b38f-4155-a93e-d89117d28269'
        ]
    )]
    public ?string $createdBy = null;

    #[ApiProperty(
        readable: true,
        writable: false,
        openapiContext: [
            'type' => 'string',
            'example' => '/api/system/users/95348868-b38f-4155-a93e-d89117d28269'
        ]
    )]
    public ?string $updatedBy = null;
}
