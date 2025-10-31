<?php

namespace Marvin\Device\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;

class DeviceCompositeCircularReference extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
    ) {
        parent::__construct($message);
    }

    public function translationId(): string
    {
        return 'device.exceptions.DE0005.device_composite_circular_reference';
    }

    public function translationParameters(): array
    {
        return [];
    }

    public function translationDomain(): string
    {
        return 'device';
    }
}
