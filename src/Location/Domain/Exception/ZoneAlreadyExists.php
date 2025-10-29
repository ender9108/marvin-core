<?php

namespace Marvin\Location\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;

final class ZoneAlreadyExists extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        string $code,
        public readonly ?string $zoneName = null,
    ) {
        parent::__construct($message, $code);
    }

    public static function withLabel(string $zoneName): self
    {
        return new self(
            sprintf('Zone with name %s already exists', $zoneName),
            'LO0005',
            $zoneName,
        );
    }

    #[Override]
    public function translationId(): string
    {
        if (null !== $this->zoneName) {
            return 'location.exceptions.zone_already_exists_with_name';
        }
        return 'location.exceptions.zone_already_exists';
    }

    #[Override]
    /** @return array<string, string|null> */
    public function translationParameters(): array
    {
        return [
            '%name%' => $this->zoneName,
        ];
    }

    #[Override]
    public function translationDomain(): string
    {
        return 'location';
    }
}
