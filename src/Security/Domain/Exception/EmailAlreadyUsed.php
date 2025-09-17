<?php

namespace Marvin\Security\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;

class EmailAlreadyUsed extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(
        private readonly string $email
    ) {
        parent::__construct(sprintf('Email "%s" already used', $email));
    }

    public static function withEmail(string $email): self
    {
        return new self($email);
    }

    public function translationId(): string
    {
        return 'security.exceptions.email_already_used';
    }

    /** @return array<string, string> */
    public function translationParameters(): array
    {
        return [
            '%email%' => $this->email,
        ];
    }

    public function translationDomain(): string
    {
        return 'security';
    }
}
