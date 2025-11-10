<?php

declare(strict_types=1);

namespace Marvin\Device\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;
use Marvin\Shared\Domain\ValueObject\Identity\ProtocolId;

final class ProtocolNotAvailable extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        private readonly ?string $protocolId = null,
        private readonly ?bool $isDisabled = null,
    ) {
        parent::__construct($message);
    }

    public static function withId(ProtocolId $protocolId): self
    {
        return new self(
            sprintf('Protocol not found with ID: %s', $protocolId->toString()),
            $protocolId->toString(),
        );
    }

    public static function withIsDisabled(ProtocolId $protocolId): self
    {
        return new self(
            sprintf('Protocol is disabled with ID: %s', $protocolId->toString()),
            $protocolId->toString(),
            true,
        );
    }

    public function translationId(): string
    {
        if (null !== $this->protocolId && true === $this->isDisabled) {
            return 'device.exceptions.DE0043.protocol_is_disabled';
        }

        if (null !== $this->protocolId && null === $this->isDisabled) {
            return 'device.exceptions.DE0042.protocol_not_found_with_id';
        }

        return 'device.exceptions.DE0041.protocol_not_found';
    }

    public function translationParameters(): array
    {
        return [
            '%id%' => $this->protocolId,
        ];
    }

    public function translationDomain(): string
    {
        return 'device';
    }
}
