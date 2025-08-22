<?php

namespace App\System\Infrastructure\Symfony\EventListener;

use App\System\Infrastructure\Doctrine\Repository\DoctrineUserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RequestStack;

#[AsEventListener(event: 'lexik_jwt_authentication.on_jwt_created', method: 'onJWTCreated')]
readonly class JWTListener
{
    public function __construct(
        private DoctrineUserRepository $userRepository,
        private RequestStack $requestStack,
    ) {
    }

    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $payload = $event->getData();
        $request = $this->requestStack->getCurrentRequest();
        $user = $this->userRepository->byIdentifier($payload['username']);

        $payload['firstName'] = $user->getFirstName();
        $payload['lastName'] = $user->getLastName();
        $payload['id'] = $user->getId();
        $payload['ip'] = $request?->getClientIp();

        $event->setData($payload);
    }
}
