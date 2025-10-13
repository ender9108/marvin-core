<?php

namespace Marvin\System\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;
use Marvin\Shared\Domain\ValueObject\Reference;
use Marvin\System\Domain\ValueObject\Identity\PluginId;
use Override;

final class PluginUninstallationMethodNotAllowed extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        public readonly ?string $methodName = null,
    ) {
        parent::__construct($message);
        $this->code = 'SY0007';
    }

    public static function withMethodName(string $methodName): self
    {
        return new self(
            sprintf('You cannot use this method "%s" in uninstallation mode', $methodName),
            $methodName,
        );
    }

    #[Override]
    public function translationId(): string
    {
        if (null !== $this->methodName) {
            return 'system.exceptions.plugin_uninstallation_method_not_allowed_with_method_name';
        }

        return 'system.exceptions.plugin_uninstallation_method_not_allowed';
    }

    #[Override]
    /** @return array<string, string|null> */
    public function translationParameters(): array
    {
        return [
            '%method_name%' => $this->methodName,
        ];
    }

    #[Override]
    public function translationDomain(): string
    {
        return 'system';
    }
}
