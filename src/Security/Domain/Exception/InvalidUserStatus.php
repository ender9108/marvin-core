<?php

namespace Marvin\Security\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;

class InvalidUserStatus extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        private readonly string $action,
        private readonly string $reference
    ) {
        parent::__construct(sprintf(
            'For action "%s", invalid user status provided (%s)',
            $this->action,
            $this->reference
        ));
    }

    public static function withByActionAndReference(string $action, string $reference): self
    {
        return new self($action, $reference);
    }

    public function translationId(): string
    {
        return 'security.exceptions.invalid_user_status';
    }

    /** @return array<string, string> */
    public function translationParameters(): array
    {
        return [
            '%action%' => $this->action,
            '%reference%' => $this->reference,
        ];
    }

    public function translationDomain(): string
    {
        return 'security';
    }
}
