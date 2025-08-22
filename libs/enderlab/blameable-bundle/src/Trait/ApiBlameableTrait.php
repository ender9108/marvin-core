<?php

namespace EnderLab\BlameableBundle\Trait;

use ApiPlatform\Metadata\ApiProperty;
use Symfony\Component\Serializer\Attribute\Groups;

trait ApiBlameableTrait
{
    #[ApiProperty(
        readable: true,
        writable: false,
        openapiContext: [
            'type' => 'string',
            'example' => '/api/customer/users/1'
        ]
    )]
    #[Groups('blameable:read')]
    public ?string $createdBy = null;

    #[ApiProperty(
        readable: true,
        writable: false,
        openapiContext: [
            'type' => 'string',
            'example' => '/api/customer/users/1'
        ]
    )]
    #[Groups('blameable:read')]
    public ?string $updatedBy = null;
}
