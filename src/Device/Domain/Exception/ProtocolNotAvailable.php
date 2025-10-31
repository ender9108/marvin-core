<?php

namespace Marvin\Device\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;
use Marvin\Shared\Domain\ValueObject\Identity\ProtocolId;

class ProtocolNotAvailable extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        public readonly ?string $protocolId = null,
        public readonly ?bool $isDisabled = null
    ) {
        parent::__construct($message);
    }

    public static function withId(ProtocolId $protocolId): self
    {
        return new self(
            sprintf('The protocol %d is not available', $protocolId->toString()),
            $protocolId->toString(),
        );
    }

    public static function withIsDisabled(ProtocolId $protocolId): self
    {
        return new self(
            sprintf('The protocol %d is not enabled', $protocolId->toString()),
            $protocolId->toString(),
            true
        );
    }

    public function translationId(): string
    {
        if (null !== $this->protocolId && null === $this->isDisabled) {
            return 'device.exceptions.DE0012.protocol_not_available_with_id';
        }

        if (null !== $this->protocolId && null !== $this->isDisabled) {
            return 'device.exceptions.DE0013.protocol_is_disabled';
        }

        return 'device.exceptions.DE0011.protocol_not_available';
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
