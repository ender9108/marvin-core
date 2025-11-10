<?php

namespace Marvin\Protocol\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;
use Marvin\Shared\Domain\ValueObject\Identity\ProtocolId;

class ProtocolNotFoundException extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        public readonly ?string $id = null,
    ) {
        parent::__construct($message);
    }

    public static function withId(ProtocolId $id): self
    {
        return new self(
            sprintf('The protocol %s is not found', $id->toString()),
            $id->toString(),
        );
    }

    public function translationId(): string
    {
        if (null !== $this->id) {
            return 'protocol.exceptions.PR0002.protocol_not_found_with_id';
        }

        return 'protocol.exceptions.PR0001.protocol_not_found';
    }

    public function translationParameters(): array
    {
        return [
            '%id%' => $this->id,
        ];
    }

    public function translationDomain(): string
    {
        return 'protocol';
    }
}
