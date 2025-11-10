<?php

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
        $payload['id'] = $user->id->toString();
        $payload['ip'] = $request?->getClientIp();

        $event->setData($payload);
    }
}
