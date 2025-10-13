<?php

namespace Marvin\System\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;
use Override;

final class MqttSubscribeError extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        private array $topics,
    ) {
        parent::__construct($message);
        $this->code = 'SY0009';
    }

    public static function withTopic(array $topics): self
    {
        return new self(
            sprintf('Error while subscribing to topic %s', implode(', ', $topics)),
            $topics,
        );
    }

    #[Override]
    public function translationId(): string
    {
        return 'security.exceptions.mqtt_subscribe_error';
    }

    #[Override]
    /** @return array<string, string> */
    public function translationParameters(): array
    {
        return [
            '%topics%' => implode(', ', $this->topics),
        ];
    }

    #[Override]
    public function translationDomain(): string
    {
        return 'system';
    }
}
