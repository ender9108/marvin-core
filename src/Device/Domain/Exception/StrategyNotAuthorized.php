<?php

namespace Marvin\Device\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;
use Marvin\Device\Domain\ValueObject\Protocol;
use Marvin\Shared\Domain\ValueObject\Identity\DeviceId;

class StrategyNotAuthorized extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        private readonly ?bool $withDifferentProtocols = null,
        private readonly ?bool $withoutProtocol = null,
        private readonly ?string $protocol = null,
    ) {
        parent::__construct($message);
    }

    public static function nativeOnlyWithDifferentProtocols(): self
    {
        return new self(
            'Cannot create native group: devices use different protocols. Use EMULATED_ONLY strategy for mixed protocols',
            true
        );
    }

    public static function nativeGroupWithoutProtocol(): self
    {
        return new self(
            'Cannot create native group: devices have no protocol',
            false,
            true
        );
    }

    public static function protocolDoesNotSupportNativeGroup(Protocol $protocol): self
    {
        return new self(
            'Cannot create native group: devices have no protocol',
            null,
            null,
            $protocol->value
        );
    }

    public function translationId(): string
    {
        if (true === $this->withDifferentProtocols) {
            return 'device.exceptions.DE0048.native_only_with_different_protocols';
        }

        if (true === $this->withoutProtocol) {
            return 'device.exceptions.DE0049.native_group_without_protocol';
        }

        if (null !== $this->protocol) {
            return 'device.exceptions.DE0050.protocol_does_not_support_native_group';
        }

        return $this->getMessage();
    }

    public function translationParameters(): array
    {
        return [
            '%protocol%' => $this->protocol,
        ];
    }

    public function translationDomain(): string
    {
        return 'device';
    }
}
