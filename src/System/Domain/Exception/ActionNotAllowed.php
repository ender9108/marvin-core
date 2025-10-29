<?php

namespace Marvin\System\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\System\Domain\ValueObject\Identity\ContainerId;
use Override;

final class ActionNotAllowed extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        string $message,
        string $code,
        public readonly ?string $containerLabel = null,
        public readonly ?string $action = null,
    ) {
        parent::__construct($message, $code);
    }

    public static function withContainerAndAction(Label|ContainerId $container, string $action): self
    {
        return new self(
            sprintf('Action %s not allowed for container %s', $action, $container->value),
            'SM0001',
            $container->value,
            $action
        );
    }

    #[Override]
    public function translationId(): string
    {
        if (null !== $this->containerLabel && null !== $this->action) {
            return 'system.exceptions.action_not_allowed_with_label_and_action';
        }
        return 'system.exceptions.action_request_not_found';
    }

    #[Override]
    /** @return array<string, string|null> */
    public function translationParameters(): array
    {
        return [
            '%containerLabel%' => $this->containerLabel,
            '%action%' => $this->action,
        ];
    }

    #[Override]
    public function translationDomain(): string
    {
        return 'system';
    }
}
