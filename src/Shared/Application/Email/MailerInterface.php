<?php

namespace Marvin\Shared\Application\Email;

interface MailerInterface
{
    public function send(EmailDefinitionInterface $email): void;
}
