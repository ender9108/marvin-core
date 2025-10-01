<?php

namespace Marvin\Security\Application\CommandHandler\User;

use DateMalformedStringException;
use EnderLab\DddCqrsBundle\Application\Command\SyncCommandHandlerInterface;
use Marvin\Security\Application\Command\User\RequestResetPasswordUser;
use Marvin\Security\Domain\Model\RequestResetPassword;
use Marvin\Security\Domain\Model\User;
use Marvin\Security\Domain\Repository\RequestResetPasswordRepositoryInterface;
use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use Marvin\Shared\Application\Email\Mailer;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class RequestResetPasswordUserHandler implements SyncCommandHandlerInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private RequestResetPasswordRepositoryInterface $requestResetPasswordRepository,
        private Mailer $mailer,
    ) {
    }

    /**
     * @throws DateMalformedStringException
     */
    public function __invoke(RequestResetPasswordUser $command): User
    {
        $user = $this->userRepository->byEmail($command->email);
        $token = bin2hex(openssl_random_pseudo_bytes(16));
        $request = new RequestResetPassword($token, $user);

        $this->requestResetPasswordRepository->save($request);

        $email = '';

        return $user;
    }
}
