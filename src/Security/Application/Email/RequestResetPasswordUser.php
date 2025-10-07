<?php

namespace Marvin\Security\Application\Email;

use Marvin\Security\Domain\ValueObject\Firstname;
use Marvin\Security\Domain\ValueObject\Lastname;
use Marvin\Shared\Application\Email\EmailDefinitionInterface;
use Marvin\Shared\Domain\ValueObject\Email;
use Marvin\Shared\Domain\ValueObject\Locale;

final readonly class RequestResetPasswordUser implements EmailDefinitionInterface
{
    public function __construct(
        private Email $recipient,
        private Firstname $firstname,
        private Lastname $lastname,
        private Locale $userLocale,
        private string $token,
    ) {
    }

    public function recipient(): Email
    {
        return $this->recipient;
    }

    public function subject(): string
    {
        return 'security.email.request_change_password.subject';
    }

    public function subjectVariables(): array
    {
        return [];
    }

    public function template(): string
    {
        return '@security_mail/request_reset_password.html.twig';
    }

    public function templateVariables(): array
    {
        return [
            'token' => $this->token,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
        ];
    }

    public function locale(): string
    {
        return $this->userLocale->value;
    }

    public function getDomain(): string
    {
        return 'security';
    }
}
