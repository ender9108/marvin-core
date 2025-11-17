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

namespace Marvin\System\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Marvin\System\Domain\ValueObject\Identity\WorkerId;
use Override;
use Symfony\Bridge\Doctrine\Types\AbstractUidType;

final class WorkerIdType extends AbstractUidType
{
    #[Override]
    public function getName(): string
    {
        return 'worker_id';
    }

    #[Override]
    protected function getUidClass(): string
    {
        return WorkerId::class;
    }
}
