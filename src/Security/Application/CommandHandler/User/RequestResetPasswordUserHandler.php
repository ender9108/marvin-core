<?php

namespace Marvin\Security\Application\CommandHandler\User;

use DateMalformedStringException;
use Marvin\Security\Application\Command\User\RequestResetPasswordUser;
use Marvin\Security\Application\Email\RequestResetPasswordUser as EmailRequestResetPasswordUser;
use Marvin\Security\Domain\Exception\RequestResetPasswordAlreadyExists;
use Marvin\Security\Domain\Model\RequestResetPassword;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\Repository\RequestResetPasswordRepositoryInterface;
use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use Marvin\Shared\Application\Email\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class RequestResetPasswordUserHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private RequestResetPasswordRepositoryInterface $requestResetPasswordRepository,
        private MailerInterface $mailer,
    ) {
    }

    /**
     * @throws DateMalformedStringException
     */
    public function __invoke(RequestResetPasswordUser $command): User
    {
        $user = $this->userRepository->byEmail($command->email);

        if (true === $this->requestResetPasswordRepository->checkIfRequestAlreadyExists($user)) {
            throw new RequestResetPasswordAlreadyExists(
                sprintf('Reset password request already exists for this user (id: %s).', $user->id->toString())
            );
        }

        $token = bin2hex(openssl_random_pseudo_bytes(16));
        $request = new RequestResetPassword($token, $user);

        $this->requestResetPasswordRepository->save($request);

        $email = new EmailRequestResetPasswordUser(
            $user->email,
            $user->firstname,
            $user->lastname,
            $user->locale,
            $token,
        );

        $this->mailer->send($email);

        return $user;
    }
}
