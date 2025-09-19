<?php

namespace EnderLab\DddCqrsBundle\Infrastructure\Framework\Symfony\Messenger\Middleware;

use EnderLab\DddCqrsBundle\Domain\Event\Attribute\AsDomainEvent;
use EnderLab\DddCqrsBundle\Application\Event\Attribute\AsDomainEventHandler;
use EnderLab\DddCqrsBundle\Domain\Event\DomainEventInterface;
use Psr\Log\LoggerInterface;
use ReflectionException;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpReceivedStamp;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use ReflectionClass;
use Symfony\Component\Messenger\Stamp\TransportNamesStamp;

readonly class DomainEventRoutingMiddleware implements MiddlewareInterface
{
    /**
     * @param iterable<object> $handlers
     */
    public function __construct(
        private readonly LoggerInterface $logger,
        #[AutowireIterator('enderlab.domain_event_routing_key_handlers')]
        private iterable $handlers
    ) {
    }

    /**
     * @throws ReflectionException
     * @throws ExceptionInterface
     */
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $message = $envelope->getMessage();
        $reflectionMessage = new ReflectionClass($message);
dd($message);
        // 1. PUBLISH
        if (!$envelope->last(AmqpReceivedStamp::class)) {
            if ($message instanceof DomainEventInterface) {
                $envelope = $envelope->with(
                    new TransportNamesStamp('domain.event'),
                    new AmqpStamp($message::getRoutingKey(), AMQP_NOPARAM, [])
                );

                $this->logger->info(sprintf(
                    'Dispatch message %s to routingKey %s.',
                    get_class($message),
                    $message::getRoutingKey()
                ));
            }

            // Do not handle locally on publish: let default SendMessage middleware send it to transport.
            return $envelope;
        }

        // 2. CONSUME
        $amqpStamp = $envelope->last(AmqpReceivedStamp::class);
        if (!$amqpStamp || !$amqpStamp->getAmqpEnvelope()->getRoutingKey()) {
            return $stack->next()->handle($envelope, $stack);
        }

        $routingKey = $amqpStamp->getAmqpEnvelope()->getRoutingKey();

        foreach ($this->handlers as $handler) {;
            $reflectionHandler = new ReflectionClass($handler);
            $parentClass = $reflectionHandler->getParentClass();
            $attributes = $parentClass
                ? $parentClass->getAttributes(AsDomainEventHandler::class)
                : $reflectionHandler->getAttributes(AsDomainEventHandler::class)
            ;

            foreach ($attributes as $attr) {
                /** @var AsDomainEventHandler $instance */
                $instance = $attr->newInstance();

                if (in_array($routingKey, $instance->routingKeys, true)) {
                    $this->logger->info(sprintf(
                        'Dispatching message %s to handler "%s".',
                        $reflectionMessage->getName(),
                        $reflectionHandler->getParentClass()->getName()
                    ));

                    return $stack->next()->handle($envelope, $stack);
                }
            }
        }

        return $envelope;
    }
}
