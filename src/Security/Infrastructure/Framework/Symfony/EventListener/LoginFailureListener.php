<?php
namespace Marvin\Security\Infrastructure\Framework\Symfony\EventListener;

use Marvin\Security\Application\Command\User\UserLoginAttempt;
use Marvin\Security\Infrastructure\Framework\Symfony\Security\SecurityUser;
use EnderLab\DddCqrsBundle\Application\Command\CommandBusInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;

#[AsEventListener(LoginFailureEvent::class)]
final readonly class LoginFailureListener
{
    public function __construct(
        private CommandBusInterface $commandBus
    ) {
    }

    public function __invoke(LoginFailureEvent $event): void
    {
        /** @var SecurityUser|null $user */
        $user = $event->getPassport()?->getUser();

        if ($user && $event->getException() instanceof BadCredentialsException) {
            $this->commandBus->dispatch(new UserLoginAttempt($user->id));
        }
    }
}
