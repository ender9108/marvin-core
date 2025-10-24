<?php

namespace Marvin\Device\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;
use Marvin\Device\Domain\ValueObject\Capability;

class CapabilityNotSupportedAction extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        string $code,
        public readonly ?string $capability = null,
        public readonly ?string $action = null,
    ) {
        parent::__construct($message, $code);
    }

    public static function withCapabilityAndAction(Capability $capability, string $action): self
    {
        return new self(
            sprintf('The capability %s does not supported action %s', $capability->value, $action),
            'D00014',
            $capability->value,
            $action
        );
    }

    public function translationId(): string
    {
        if (null !== $this->capability) {
            return 'device.exceptions.capability_not_supported_action_with_capability_name_and_action';
        }

        return 'device.exceptions.capability_not_supported_action';
    }

    public function translationParameters(): array
    {
        return [
            '%capability%' => $this->capability
        ];
    }

    public function translationDomain(): string
    {
        return 'device';
    }
}
