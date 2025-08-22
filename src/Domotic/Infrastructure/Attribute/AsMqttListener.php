<?php

namespace App\Domotic\Infrastructure\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class AsMqttListener
{
    public function __construct(
        public string $event,
        public int $priority = 255
    ) {
    }
}
