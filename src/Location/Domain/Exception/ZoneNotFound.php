<?php

namespace Marvin\Location\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;
use Marvin\Shared\Domain\ValueObject\Identity\ZoneId;
use Override;

final class ZoneNotFound extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        string $code,
        public readonly ?string $id = null,
    ) {
        parent::__construct($message, $code);
    }

    public static function withId(ZoneId $id): self
    {
        return new self(
            sprintf('Zone with id %s was not found', $id->toString()),
            'LO0006',
            $id->toString(),
        );
    }

    #[Override]
    public function translationId(): string
    {
        if (null !== $this->id) {
            return 'location.exceptions.zone_not_found_with_id';
        }
        return 'location.exceptions.zone_not_found';
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
        return 'location';
    }
}
