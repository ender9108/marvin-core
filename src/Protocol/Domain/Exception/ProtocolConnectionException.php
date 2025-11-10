<?php

namespace Marvin\Protocol\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;
use Marvin\Shared\Domain\ValueObject\Identity\ProtocolId;

class ProtocolConnectionException extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        public readonly ?string $protocolId = null,
        public readonly ?string $errorMessage = null,
    ) {
        parent::__construct($message);
    }

    public static function withProtocol(ProtocolId $protocolId, string $errorMessage): self
    {
        return new self(
            sprintf('Connection error for protocol %s: %s', $protocolId->toString(), $errorMessage),
            $protocolId->toString(),
            $errorMessage,
        );
    }

    public function translationId(): string
    {
        if (null !== $this->protocolId) {
            return 'protocol.exceptions.PR0004.protocol_connection_error_with_id';
        }

        return 'protocol.exceptions.PR0003.protocol_connection_error';
    }

    public function translationParameters(): array
    {
        return [
            '%protocol_id%' => $this->protocolId,
            '%error_message%' => $this->errorMessage,
        ];
    }

    public function translationDomain(): string
    {
        return 'protocol';
    }
}
