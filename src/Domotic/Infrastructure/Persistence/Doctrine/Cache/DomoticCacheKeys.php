<?php

namespace Marvin\Domotic\Infrastructure\Persistence\Doctrine\Cache;

use Symfony\Component\Uid\UuidV7;

enum DomoticCacheKeys: string
{
    case CAPABILITY_ITEM = 'capability_item_%s';
    case CAPABILITY_LIST = 'capability_list';

    public function withId(UuidV7 $id): string
    {
        return sprintf($this->value, $id->toString());
    }
}
