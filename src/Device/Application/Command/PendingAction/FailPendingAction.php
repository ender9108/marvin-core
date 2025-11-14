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

namespace Marvin\Device\Application\Command\PendingAction;

use EnderLab\DddCqrsBundle\Application\Command\CommandInterface;
use Marvin\Shared\Domain\ValueObject\Identity\CorrelationId;

/**
 * FailPendingAction - Command
 *
 * Marks a pending action as failed with error message
 */
final readonly class FailPendingAction implements CommandInterface
{
    public function __construct(
        public CorrelationId $correlationId,
        public string $errorMessage,
    ) {
    }
}
