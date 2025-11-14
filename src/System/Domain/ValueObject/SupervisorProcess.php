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

use Enderlab\DddCqrsBundle\Domain\Assert\Assert;
use Stringable;

final readonly class SupervisorProcess implements Stringable
{
    private string $value;

    public function __construct(string $value)
    {
        Assert::notEmpty($value, 'shared.exceptions.SY0012.supervisor_process_does_not_empty');
        Assert::regex($value, '/^[a-z0-9_\-]+$/i', 'shared.exceptions.SY0013.supervisor_process_is_not_valid');
        ;

        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
