<?php

namespace Marvin\Security\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;
use Marvin\Shared\Domain\ValueObject\Email;
use Marvin\Shared\Domain\ValueObject\Identity\UserId;
use Override;

final class UserNotFound extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        public readonly ?string $email = null,
        public readonly ?string $id = null,
    ) {
        parent::__construct($message);
    }

    public static function withEmail(Email $email): self
    {
        return new self(
            sprintf('User with email %s was not found', $email->value),
            $email->value,
        );
    }

    public static function withId(UserId $userId): self
    {
        return new self(
            sprintf('User with id %s was not found', $userId->toString()),
            null,
            $userId->toString(),
        );
    }

    #[Override]
    public function translationId(): string
    {
        if (null !== $this->email) {
            return 'security.exceptions.SC0003.user_not_found_with_email';
        }

        if (null !== $this->id) {
            return 'security.exceptions.SC0004.user_not_found_with_id';
        }

        return 'security.exceptions.SC0002.user_not_found';
    }

    #[Override]
    /** @return array<string, string> */
    public function translationParameters(): array
    {
        return [
            '%email%' => $this->email,
            '%id%' => $this->id,
        ];
    }

    #[Override]
    public function translationDomain(): string
    {
        return 'security';
    }
}
