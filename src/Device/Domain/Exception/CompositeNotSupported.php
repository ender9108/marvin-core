<?php

namespace Marvin\Device\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;

final class CompositeNotSupported extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        private readonly ?string $protocolType = null,
        private readonly ?string $messageType = null,
    ) {
        parent::__construct($message);
    }

    public static function nativeOnlyButNotSupported(string $protocolType): self
    {
        return new self(
            sprintf('Strategy NATIVE_ONLY requires native support, but protocol %s does not support it.', $protocolType),
            $protocolType
        );
    }

    public static function mixedProtocols(): self
    {
        return new self(
            'Cannot create native group/scene with mixed protocols. Use EMULATED_ONLY strategy.',
            null,
            'mixed_protocols'
        );
    }

    public static function noCommonProtocol(): self
    {
        return new self(
            'No common protocol found among child devices. Native group/scene not possible.',
            null,
            'no_common_protocol'
        );
    }

    public function translationId(): string
    {
        if (null !== $this->protocolType) {
            return 'device.exceptions.DE0014.composite_not_supported_with_message_type';
        }

        if ($this->messageType === 'mixed_protocols') {
            return 'device.exceptions.DE0015.composite_not_supported_mixed_protocols';
        }

        if ($this->messageType === 'no_common_protocol') {
            return 'device.exceptions.DE0016.composite_not_supported_no_common_protocol';
        }

        return 'device.exceptions.DE0017.composite_not_supported';
    }

    public function translationParameters(): array
    {
        return [
            '%protocol_type%' => $this->protocolType,
            '%message_type%' => $this->messageType,
        ];
    }

    public function translationDomain(): string
    {
        return 'device';
    }
}
