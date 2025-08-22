<?php

namespace EnderLab\DddCqrsBundle\Application\Query\Bus;

use EnderLab\DddCqrsBundle\Application\Query\QueryInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class QueryBus
{
    public function __construct(
        #[Autowire(service: 'query.bus')]
        private MessageBusInterface $queryBus,
    ) {}

    /**
     * @throws ExceptionInterface
     */
    public function ask(QueryInterface $query): mixed
    {
        $envelope = $this->queryBus->dispatch($query);
        $handled = $envelope->last(HandledStamp::class);

        return $handled?->getResult();
    }
}
