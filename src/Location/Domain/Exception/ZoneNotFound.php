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
        public readonly ?string $id = null,
    ) {
        parent::__construct($message);
    }

    public static function withId(ZoneId $id): self
    {
        return new self(
            sprintf('Zone with id %s was not found', $id->toString()),
            $id->toString(),
        );
    }

    #[Override]
    public function translationId(): string
    {
        if (null !== $this->id) {
            return 'location.exceptions.LO0002.zone_not_found_with_id';
        }
        return 'location.exceptions.LO0001.zone_not_found';
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
