<?php

declare(strict_types=1);

namespace Marvin\Device\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;

final class FailedCreateNativeGroup extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        private readonly ?string $error = null,
    ) {
        parent::__construct($message);
    }

    public function translationId(): string
    {
        return 'device.exceptions.DE0051.failed_create_native_group';
    }

    public function translationParameters(): array
    {
        return [
            '%error%' => $this->error,
        ];
    }

    public function translationDomain(): string
    {
        return 'device';
    }
}
