<?php

namespace Marvin\System\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;
use Override;

final class MqttLoopError extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        private readonly string $error
    ) {
        parent::__construct($message);
        $this->code = 'SY0011';
    }

    public static function withError(string $error): self
    {
        return new self(
            sprintf('Error mqtt loop %s', $error),
            $error
        );
    }

    #[Override]
    public function translationId(): string
    {
        return 'security.exceptions.mqtt_loop_error';
    }

    #[Override]
    /** @return array<string, string> */
    public function translationParameters(): array
    {
        return [
            '%error%' => $this->error
        ];
    }

    #[Override]
    public function translationDomain(): string
    {
        return 'system';
    }
}
