<?php

namespace Marvin\Secret\Infrastructure\Persistence\Doctrine\Cache;

use Symfony\Component\Uid\UuidV7;

enum SecretCacheKeys: string
{
    case SECRET_ITEM = 'secret_item_%s';
    case SECRET_LIST = 'secret_list';

    public function withId(UuidV7 $id): string
    {
        return sprintf($this->value, $id->toString());
    }
}
