<?php
namespace EnderLab\DddCqrsApiPlatformBundle\Infrastructure\Symfony\Security\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY|Attribute::IS_REPEATABLE)]
class FieldUpdatableBy
{
    public function __construct(
        public array $roles = [],
        public string $message = 'You are not authorized to modify the field "%s".'
    ) {}
}
