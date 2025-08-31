<?php
namespace EnderLab\MarvinManagerBundle\System\Infrastructure\Symfony\Messenger\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class AsMessageType
{
    public function __construct(
        public ?string $binding = null,
    ) {
    }
}
