<?php

namespace Marvin\Shared\Application\Email;

interface Mailer
{
    public function send(EmailDefinition $email): void;
}
