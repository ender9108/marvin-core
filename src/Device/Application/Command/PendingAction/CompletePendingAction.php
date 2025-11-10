<?php

declare(strict_types=1);

namespace Marvin\Device\Application\Command\PendingAction;

use EnderLab\DddCqrsBundle\Application\Command\CommandInterface;
use Marvin\Shared\Domain\ValueObject\Identity\CorrelationId;

/**
 * CompletePendingAction - Command
 *
 * Marks a pending action as completed with result
 */
final readonly class CompletePendingAction implements CommandInterface
{
    public function __construct(
        public CorrelationId $correlationId,
        public array $result,
    ) {
    }
}
