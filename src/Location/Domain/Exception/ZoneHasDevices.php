<?php

namespace Marvin\Location\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;

final class ZoneHasDevices extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        public readonly ?string $label = null,
        public readonly ?int $deviceCount = null
    ) {
        parent::__construct($message);
    }

    public static function cannotDelete(string $label, int $deviceCount): self
    {
        return new self(
            sprintf('Cannot delete zone %s because it has %d devices', $label, $deviceCount),
            $label,
            $deviceCount,
        );
    }

    public function translationId(): string
    {
        // TODO: Implement translationId() method.
    }

    public function translationParameters(): array
    {
        return [
            '%name%' => $this->label,
            '%%' => $this->deviceCount
        ];
    }

    public function translationDomain(): string
    {
        return 'location';
    }
}
