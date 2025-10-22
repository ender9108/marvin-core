<?php

namespace Marvin\Device\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;
use Marvin\Shared\Domain\ValueObject\Identity\ProtocolId;

class ProtocolNotAvailable extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        string $code,
        public readonly ?string $protocolId = null,
        public readonly ?bool $isDisabled = null
    ) {
        parent::__construct($message, $code);
    }

    public static function withId(ProtocolId $protocolId): self
    {
        return new self(
            sprintf('The protocol %d is not available', $protocolId->toString()),
            'D00006',
            $protocolId->toString(),
        );
    }

    public static function withIsDisabled(ProtocolId $protocolId): self
    {
        return new self(
            sprintf('The protocol %d is not enabled', $protocolId->toString()),
            'D00007',
            $protocolId->toString(),
            true
        );
    }

    public function translationId(): string
    {
        if (null !== $this->protocolId && null === $this->isDisabled) {
            return 'device.exceptions.protocol_not_available_with_id';
        }

        if (null !== $this->protocolId && null !== $this->isDisabled) {
            return 'device.exceptions.protocol_is_disabled';
        }

        return 'device.exceptions.protocol_not_available';
    }

    public function translationParameters(): array
    {
        return [
            '%id%' => $this->protocolId
        ];
    }

    public function translationDomain(): string
    {
        return 'device';
    }
}
