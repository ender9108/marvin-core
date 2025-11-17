<?php

/**
 * Marvin Core - DDD-based home automation system
 *
 * @package   Marvin\Core
 * @author    Alexandre Berthelot <alexandreberthelot9108@gmail.com>
 * @copyright 2024-present Alexandre Berthelot
 * @license   AGPL-3.0 License
 * @link      https://github.com/ender9108/marvin-core
 */

declare(strict_types=1);

namespace Marvin\Security\Infrastructure\Framework\Symfony\EventListener;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use Marvin\Security\Application\Command\User\UserLoginAttempt;
use Marvin\Security\Infrastructure\Framework\Symfony\Security\SecurityUser;
use Marvin\Shared\Domain\ValueObject\Identity\UserId;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;

#[AsEventListener(LoginFailureEvent::class)]
final readonly class LoginFailureListener
{
    public function __construct(
        private SyncCommandBusInterface $commandBus
    ) {
    }

    public function __invoke(LoginFailureEvent $event): void
    {
        /** @var SecurityUser|null $user */
        $user = $event->getPassport()?->getUser();

        if ($user && $event->getException() instanceof BadCredentialsException) {
            $this->commandBus->handle(new UserLoginAttempt(UserId::fromString($user->id)));
        }
    }
}
