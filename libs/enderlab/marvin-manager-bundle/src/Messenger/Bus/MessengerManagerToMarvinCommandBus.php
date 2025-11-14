<?php

declare(strict_types=1);

namespace EnderLab\MarvinManagerBundle\Messenger\Bus;

use EnderLab\MarvinManagerBundle\Messenger\Response\ManagerResponseCommandInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\BusNameStamp;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;
use Symfony\Component\Messenger\Stamp\TransportNamesStamp;

final readonly class MessengerManagerToMarvinCommandBus implements ManagerToMarvinCommandBusInterface
{
    public function __construct(
        private MessageBusInterface $messageBus
    ) {
    }
    /**
     * @throws ExceptionInterface
     */
    public function dispatch(ManagerResponseCommandInterface $command): void
    {
        $this->messageBus->dispatch(
            new Envelope($command)
                ->with(
                    new DispatchAfterCurrentBusStamp(),
                    new BusNameStamp('manager.to.marvin'),
                    new TransportNamesStamp('manager.to.marvin')
                )
        );
    }
}
