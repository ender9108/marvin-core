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

namespace Marvin\Device\Application\Query\Scene;

use EnderLab\DddCqrsBundle\Application\Query\QueryInterface;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;

/**
 * Query to get all scenes
 */
final readonly class GetScenesCollection implements QueryInterface
{
    public function __construct(
        public ?ZoneId $zoneId = null,
        public int $page = 1,
        public int $limit = 50,
    ) {
    }
}
