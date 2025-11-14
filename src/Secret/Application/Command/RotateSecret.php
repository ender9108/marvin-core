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

namespace Marvin\Secret\Application\Command;

use EnderLab\DddCqrsBundle\Application\Command\CommandInterface;
use Marvin\Secret\Domain\ValueObject\SecretKey;

final readonly class RotateSecret implements CommandInterface
{
    public function __construct(
        public SecretKey $key,
        public ?string $newValue = null, // Si null, génère automatiquement
    ) {
    }
}
