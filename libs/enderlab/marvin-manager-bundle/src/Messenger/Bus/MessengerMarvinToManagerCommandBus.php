<?php
namespace EnderLab\MarvinManagerBundle\Messenger\Bus;

use EnderLab\MarvinManagerBundle\Messenger\Request\ManagerRequestCommandInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\BusNameStamp;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;
use Symfony\Component\Messenger\Stamp\TransportNamesStamp;

final readonly class MessengerMarvinToManagerCommandBus implements MarvinToManagerCommandBusInterface
{
    public function __construct(
        private MessageBusInterface $messageBus
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function dispatch(ManagerRequestCommandInterface $command): void
    {
        $this->messageBus->dispatch(
            new Envelope($command)
                ->with(
                    new DispatchAfterCurrentBusStamp(),
                    new BusNameStamp('marvin.to.manager'),
                    new TransportNamesStamp('marvin.to.manager')
                )
        );
    }
}
