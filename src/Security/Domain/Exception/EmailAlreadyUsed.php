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
use Marvin\Shared\Domain\ValueObject\Email;

class EmailAlreadyUsed extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        private readonly string $email
    ) {
        parent::__construct($message);
    }

    public static function withEmail(Email $email): self
    {
        return new self(
            sprintf('Email "%s" already used', $email->value),
            $email->value
        );
    }

    public function translationId(): string
    {
        return 'security.exceptions.SC0013.email_already_used';
    }

    /** @return array<string, string> */
    public function translationParameters(): array
    {
        return [
            '%email%' => $this->email,
        ];
    }

    public function translationDomain(): string
    {
        return 'security';
    }
}
