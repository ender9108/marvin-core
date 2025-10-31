<?php

namespace Marvin\Device\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;

class DeviceMustBeComposite extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
    ) {
        parent::__construct($message);
    }

    public function translationId(): string
    {
        return 'device.exceptions.DE0004.device_must_be_composite';
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
