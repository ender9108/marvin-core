<?php
/**
 * Marvin Core - DDD-based home automation system
 *
 * @package   Marvin\Core
 * @author    Alexandre Berthelot <alexandreberthelot9108@gmail.com>
 * @copyright 2024-present Alexandre Berthelot
 * @license   AGPL-3.0 License
 * @link      https://github.com/ender9108/marvin-core
 */

declare(strict_types=1);

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
