<?php

namespace Marvin\System\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;
use Marvin\System\Domain\ValueObject\Identity\ContainerId;
use Override;

final class ContainerNotFound extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        string $code,
        public readonly ?string $id = null,
    ) {
        parent::__construct($message, $code);
    }

    public static function withId(ContainerId $id): self
    {
        return new self(
            sprintf('Container with id %s was not found', $id->toString()),
            'SM0004',
            $id->toString(),
        );
    }

    #[Override]
    public function translationId(): string
    {
        if (null !== $this->id) {
            return 'system.exceptions.container_not_found_with_id';
        }
        return 'system.exceptions.container_not_found';
    }

    #[Override]
    /** @return array<string, string|null> */
    public function translationParameters(): array
    {
        return [
            '%id%' => $this->id,
        ];
    }

    #[Override]
    public function translationDomain(): string
    {
        return 'system';
    }
}
