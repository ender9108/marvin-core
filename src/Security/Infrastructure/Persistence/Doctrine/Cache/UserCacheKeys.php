<?php

namespace Marvin\Security\Infrastructure\Persistence\Doctrine\Cache;

use Marvin\Security\Domain\ValueObject\Identity\UserId;

enum UserCacheKeys: string
{
    case USER_ITEM = 'user_item_%s';
    case USER_LIST = 'user_list';

    public function withId(UserId $id): string
    {
        return sprintf($this->value, $id->toString());
    }
}
