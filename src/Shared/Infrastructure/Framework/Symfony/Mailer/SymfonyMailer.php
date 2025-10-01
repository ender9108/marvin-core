<?php

namespace Marvin\Shared\Infrastructure\Framework\Symfony\Mailer;

use Marvin\Shared\Application\Email\EmailDefinition;
use Marvin\Shared\Application\Email\Mailer;
use Marvin\Shared\Domain\Application;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class SymfonyMailer implements Mailer
{
    public function __construct(
        private MailerInterface $mailer,
        private TranslatorInterface $translator
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[\Override]
    public function send(EmailDefinition $email): void
    {
        $sender = new Address(
            Application::APP_EMAIL_FROM,
            Application::APP_EMAIL_NAME
        );

        $htmlTemplate = sprintf('emails/%s.html.twig', $email->template());
        $txtTemplate = sprintf('emails/%s.txt.twig', $email->template());

        $message = new TemplatedEmail()
            ->from($sender)
            ->to($email->recipient()->value)
            ->subject(
                $this->translator->trans(
                    $email->subject(),
                    $email->subjectVariables(),
                    $email->getDomain(),
                    $email->locale()
                )
            )
            ->htmlTemplate($htmlTemplate)
            ->textTemplate($txtTemplate)
            ->context(array_merge(
                $email->templateVariables(),
                [
                    'locale' => $email->locale(),
                    'domain' => $email->getDomain(),
                ]
            ))
        ;

        $this->mailer->send($message);
    }
}
