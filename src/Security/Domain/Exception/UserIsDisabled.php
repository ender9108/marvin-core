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

namespace Marvin\Security\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\Interfaces\TranslatableExceptionInterface;

class UserIsDisabled extends DomainException implements TranslatableExceptionInterface
{
    public function translationId(): string
    {
        return 'security.exceptions.SC0015.user_disabled';
    }

    /** @return array<string, string> */
    public function translationParameters(): array
    {
        return [];
    }

    public function translationDomain(): string
    {
        return 'security';
    }
}
