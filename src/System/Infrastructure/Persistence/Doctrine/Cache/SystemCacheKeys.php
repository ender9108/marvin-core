<?php

namespace Marvin\System\Infrastructure\Persistence\Doctrine\Cache;

use Symfony\Component\Uid\UuidV7;

enum SystemCacheKeys: string
{
    case PLUGIN_ITEM = 'plugin_item_%s';
    case PLUGIN_LIST = 'plugin_list';

    public function withId(UuidV7 $id): string
    {
        return sprintf($this->value, $id->toString());
    }
}
