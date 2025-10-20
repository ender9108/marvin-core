<?php

namespace Marvin\Location\Infrastructure\Persistence\Doctrine\Cache;

use Symfony\Component\Uid\UuidV7;

enum LocationCacheKeys: string
{
    case ZONE_ITEM = 'zone_item_%s';
    case ZONE_LIST = 'zone_list';

    public function withId(UuidV7 $id): string
    {
        return sprintf($this->value, $id->toString());
    }
}
