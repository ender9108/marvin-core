<?php

namespace Marvin\Security\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;
use Marvin\Security\Domain\ValueObject\Identity\UserStatusId;
use Marvin\Shared\Domain\ValueObject\Reference;
use Override;

final class UserStatusNotFound extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        public readonly ?string $reference = null,
        public readonly ?string $id = null,
    ) {
        parent::__construct($message);
    }

    public static function withReference(Reference $reference): self
    {
        return new self(
            sprintf('User status with reference %s was not found', $reference->value),
            $reference->value,
        );
    }

    public static function withId(UserStatusId $userStatusId): self
    {
        return new self(
            sprintf('User status with id %s was not found', $userStatusId->toString()),
            null,
            $userStatusId->toString(),
        );
    }

    #[Override]
    public function translationId(): string
    {
        if (null !== $this->reference) {
            return 'security.exceptions.user_status_not_found_with_reference';
        }

        if (null !== $this->id) {
            return 'security.exceptions.user_status_not_found_with_id';
        }

        return 'security.exceptions.user_status_not_found';
    }

    #[Override]
    /** @return array<string, string> */
    public function translationParameters(): array
    {
        return [
            '%reference%' => $this->reference,
            '%id%' => $this->id,
        ];
    }

    #[Override]
    public function translationDomain(): string
    {
        return 'security';
    }
}
