<?php

namespace Marvin\System\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;
use Override;

final class MqttPublishError extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        private readonly string $topic,
        private readonly int $qos,
        private readonly bool $retain,
    ) {
        parent::__construct($message);
        $this->code = 'SY0010';
    }

    public static function withParameters(string $topic, int $qos, bool $retain): self
    {
        return new self(
            sprintf('Error while subscribing to topic %s', $topic),
            $topic,
            $qos,
            $retain,
        );
    }

    #[Override]
    public function translationId(): string
    {
        return 'security.exceptions.mqtt_public_error';
    }

    #[Override]
    /** @return array<string, string> */
    public function translationParameters(): array
    {
        return [
            '%topic%' => $this->topic,
            '%qos%' => $this->qos,
            '%retain%' => $this->retain,
        ];
    }

    #[Override]
    public function translationDomain(): string
    {
        return 'system';
    }
}
