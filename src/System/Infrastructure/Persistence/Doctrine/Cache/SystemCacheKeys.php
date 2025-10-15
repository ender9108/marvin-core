<?php

namespace Marvin\System\Infrastructure\Persistence\Doctrine\Cache;

use Symfony\Component\Uid\UuidV7;

enum SystemCacheKeys: string
{
    case ACTION_REQUEST_ITEM = 'action_request_item_%s';
    case ACTION_REQUEST_LIST = 'action_request_list';

    public function withId(UuidV7 $id): string
    {
        return sprintf($this->value, $id->toString());
    }
}
