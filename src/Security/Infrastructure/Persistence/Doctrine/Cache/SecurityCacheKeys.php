<?php

namespace Marvin\Security\Infrastructure\Persistence\Doctrine\Cache;

use Symfony\Component\Uid\UuidV7;

enum SecurityCacheKeys: string
{
    case USER_ITEM = 'user_item_%s';
    case USER_LIST = 'user_list';

    public function withId(UuidV7 $id): string
    {
        return sprintf($this->value, $id->toString());
    }
}
