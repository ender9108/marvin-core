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

namespace Marvin\Protocol\Application\Command;

use EnderLab\DddCqrsBundle\Application\Command\CommandInterface;
use Marvin\Protocol\Domain\ValueObject\ExecutionMode;
use Marvin\Protocol\Domain\ValueObject\ProtocolConfiguration;
use Marvin\Protocol\Domain\ValueObject\TransportType;
use Marvin\Shared\Domain\ValueObject\Label;

final readonly class RegisterProtocol implements CommandInterface
{
    public function __construct(
        public TransportType $type,
        public Label $name,
        public ProtocolConfiguration $configuration,
        public ?ExecutionMode $preferredExecutionMode = null,
    ) {
    }
}
