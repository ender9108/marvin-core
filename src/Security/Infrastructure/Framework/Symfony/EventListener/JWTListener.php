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

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RequestStack;

#[AsEventListener(event: 'lexik_jwt_authentication.on_jwt_created', method: 'onJWTCreated')]
readonly class JWTListener
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private RequestStack $requestStack,
    ) {
    }

    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $payload = $event->getData();
        $request = $this->requestStack->getCurrentRequest();
        $user = $this->userRepository->byIdentifier($payload['username']);

        $payload['firstname'] = $user->firstname->value;
        $payload['lastname'] = $user->lastname->value;
        $payload['status'] = $user->status->value;
        $payload['type'] = $user->type->value;
        $payload['locale'] = $user->locale->value;
        $payload['timezone'] = $user->timezone->value;
        $payload['theme'] = $user->theme->value;
        $payload['id'] = $user->id->toString();
        $payload['ip'] = $request?->getClientIp();

        $event->setData($payload);
    }
}
