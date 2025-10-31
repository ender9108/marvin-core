<?php

namespace Marvin\System\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;
use Marvin\Shared\Domain\ValueObject\Identity\UniqId;
use Marvin\System\Domain\ValueObject\Identity\ActionRequestId;
use Override;

final class ActionRequestNotFound extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        public readonly ?string $id = null,
        public readonly ?string $correlationId = null,
    ) {
        parent::__construct($message);
    }

    public static function withId(ActionRequestId $id): self
    {
        return new self(
            sprintf('ActionRequest with id %s was not found', $id->toString()),
            $id->toString(),
        );
    }

    public static function withCorrelationId(UniqId $correlationId): self
    {
        return new self(
            sprintf('ActionRequest with correlation id %s was not found', $correlationId->toString()),
            null,
            $correlationId->toString(),
        );
    }

    #[Override]
    public function translationId(): string
    {
        if (null !== $this->id) {
            return 'system.exceptions.action_request_not_found_with_id';
        }

        if (null !== $this->correlationId) {
            return 'system.exceptions.SY0002.action_request_not_found_with_correlation_id';
        }

        return 'system.exceptions.SY0001.action_request_not_found';
    }

    #[Override]
    /** @return array<string, string|null> */
    public function translationParameters(): array
    {
        return [
            '%id%' => $this->id,
            '%correlationId%' => $this->correlationId,
        ];
    }

    #[Override]
    public function translationDomain(): string
    {
        return 'system';
    }
}
