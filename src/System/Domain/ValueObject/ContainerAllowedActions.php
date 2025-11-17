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

namespace Marvin\System\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\MarvinManagerBundle\Reference\ManagerContainerActionReference;

final readonly class ContainerAllowedActions
{
    public array $value;

    public function __construct(array $value = [])
    {
        Assert::notEmpty($value, 'system.exceptions.SY0016.container_actions_does_not_empty');
        Assert::allInArray($value, ManagerContainerActionReference::values(), 'system.exceptions.SY0017.container_actions_is_not_available');

        $this->value = $value;
    }

    public function toArray(): array
    {
        return $this->value;
    }
}
