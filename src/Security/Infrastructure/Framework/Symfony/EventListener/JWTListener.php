<?php
namespace Marvin\Security\Infrastructure\Framework\Symfony\EventListener;

use Marvin\Security\Domain\Repository\UserRepositoryInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
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

        $payload['firstName'] = $user->firstName->firstname;
        $payload['lastName'] = $user->lastName->lastname;
        $payload['status'] = $user->status->reference->reference;
        $payload['id'] = $user->id->toString();
        $payload['ip'] = $request?->getClientIp();

        $event->setData($payload);
    }
}
