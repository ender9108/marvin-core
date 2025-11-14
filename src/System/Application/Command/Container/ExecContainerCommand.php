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

namespace Marvin\System\Application\Command\Container;

use EnderLab\DddCqrsBundle\Application\Command\CommandInterface;
use EnderLab\MarvinManagerBundle\Reference\ManagerContainerActionReference;
use Marvin\Shared\Domain\ValueObject\Identity\CorrelationId;
use Marvin\System\Domain\ValueObject\Identity\ContainerId;

final readonly class ExecContainerCommand implements CommandInterface
{
    public ManagerContainerActionReference $action;

    public function __construct(
        public ContainerId $containerId,
        public CorrelationId $correlationId,
        public int $timeout = 10,
        public string $command = '',
        public array $args = [],
    ) {
        $this->action = ManagerContainerActionReference::ACTION_EXEC_CMD;
    }
}
