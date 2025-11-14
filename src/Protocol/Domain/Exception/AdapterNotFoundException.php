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

namespace Marvin\Protocol\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\Interfaces\TranslatableExceptionInterface;
use Marvin\Shared\Domain\Exception\NotFoundInterface;
use RuntimeException;

final class AdapterNotFoundException extends DomainException implements TranslatableExceptionInterface, NotFoundInterface
{
    public function translationId(): string
    {
        // TODO: Implement translationId() method.
    }

    public function translationParameters(): array
    {
        // TODO: Implement translationParameters() method.
    }

    public function translationDomain(): string
    {
        // TODO: Implement translationDomain() method.
    }
}
