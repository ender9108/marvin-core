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
use Marvin\Security\Domain\ValueObject\Identity\RequestResetPasswordId;
use Override;

final class RequestResetPasswordExpired extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        public readonly ?string $id = null,
        public readonly ?string $token = null,
    ) {
        parent::__construct($message);
    }

    public static function withToken(string $token): self
    {
        return new self(
            sprintf('Reset request password with token %s was expired', $token),
            null,
            $token,
        );
    }

    public static function withId(RequestResetPasswordId $requestResetPasswordId): self
    {
        return new self(
            sprintf('Reset request password with id %s was expired', $requestResetPasswordId->toString()),
            null,
            $requestResetPasswordId->toString(),
        );
    }

    #[Override]
    public function translationId(): string
    {
        if (null !== $this->token) {
            return 'security.exceptions.SC0025.request_reset_password_expired_with_token';
        }

        if (null !== $this->id) {
            return 'security.exceptions.SC0024.request_reset_password_expired_with_id';
        }

        return 'security.exceptions.SC0023.request_reset_password_expired';
    }

    #[Override]
    /** @return array<string, string> */
    public function translationParameters(): array
    {
        return [
            '%token%' => $this->token,
            '%id%' => $this->id,
        ];
    }

    #[Override]
    public function translationDomain(): string
    {
        return 'security';
    }
}
