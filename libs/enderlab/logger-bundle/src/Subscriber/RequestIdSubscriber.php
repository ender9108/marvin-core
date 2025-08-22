<?php

namespace EnderLab\LoggerBundle\Subscriber;

use EnderLab\LoggerBundle\Request\RequestIdGeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

readonly class RequestIdSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private RequestIdGeneratorInterface $requestIdGenerator
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest'],
            KernelEvents::RESPONSE => ['onKernelResponse'],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!$request->headers->has('X-Request-ID')) {
            $request->headers->set('X-Request-ID', $this->requestIdGenerator->generate());
        }
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();
        $request = $event->getRequest();

        if (!$response->headers->has('X-Request-ID') && $request->headers->has('X-Request-ID')) {
            $response->headers->set('X-Request-ID', $request->headers->get('X-Request-ID'));
        }
    }
}
