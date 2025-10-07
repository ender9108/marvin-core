<?php

namespace Marvin\Shared\Infrastructure\Framework\Symfony\Mailer;

use Marvin\Shared\Application\Email\EmailDefinitionInterface;
use Marvin\Shared\Application\Email\MailerInterface as MarvinMailerInterface;
use Marvin\Shared\Domain\Application;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class SymfonyMailerInterface implements MarvinMailerInterface
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
    public function send(EmailDefinitionInterface $email): void
    {
        $sender = new Address(
            Application::APP_EMAIL_FROM,
            Application::APP_EMAIL_NAME
        );

        $template = $email->template();
        if (str_starts_with($template, '@')) {
            $htmlTemplate = $template;
            $txtTemplate = preg_replace('/\.html\.twig$/', '.txt.twig', $template) ?? $template . '.txt.twig';
        } else {
            $htmlTemplate = sprintf('emails/%s.html.twig', $template);
            $txtTemplate = sprintf('emails/%s.txt.twig', $template);
        }

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
