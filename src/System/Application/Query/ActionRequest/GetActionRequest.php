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

namespace Marvin\System\Application\Query\ActionRequest;

use EnderLab\DddCqrsBundle\Application\Query\QueryInterface;
use Marvin\System\Domain\ValueObject\Identity\ActionRequestId;

final readonly class GetActionRequest implements QueryInterface
{
    public function __construct(
        public ActionRequestId $id,
    ) {
    }
}
