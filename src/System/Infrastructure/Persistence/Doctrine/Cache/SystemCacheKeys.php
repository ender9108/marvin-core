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

namespace Marvin\System\Infrastructure\Persistence\Doctrine\Cache;

use Symfony\Component\Uid\UuidV7;

enum SystemCacheKeys: string
{
    case ACTION_REQUEST_ITEM = 'action_request_item_%s';
    case ACTION_REQUEST_LIST = 'action_request_list';
    case CONTAINER_ITEM = 'container_item_%s';
    case CONTAINER_LIST = 'container_list';
    case WORKER_ITEM = 'worker_item_%s';
    case WORKER_LIST = 'worker_list';

    public function withId(UuidV7 $id): string
    {
        return sprintf($this->value, $id->toString());
    }
}
