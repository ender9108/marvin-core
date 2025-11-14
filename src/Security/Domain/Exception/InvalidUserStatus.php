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

class InvalidUserStatus extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        private readonly string $action,
        private readonly string $reference
    ) {
        parent::__construct($message);
    }

    public static function withByActionAndReference(string $action, string $reference): self
    {
        return new self(
            sprintf(
                'For action "%s", invalid user status provided (%s)',
                $action,
                $reference
            ),
            $action,
            $reference
        );
    }

    public function translationId(): string
    {
        return 'security.exceptions.SC0001.invalid_user_status';
    }

    /** @return array<string, string> */
    public function translationParameters(): array
    {
        return [
            '%action%' => $this->action,
            '%reference%' => $this->reference,
        ];
    }

    public function translationDomain(): string
    {
        return 'security';
    }
}
