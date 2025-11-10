<?php

namespace Marvin\Device\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;

class RemoveChildrenNotAuthorized extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
    ) {
        parent::__construct($message);
    }

    public function translationId(): string
    {
        return 'device.exceptions.DE0040.remove_children_not_authorized_on_non_composite_device';
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
