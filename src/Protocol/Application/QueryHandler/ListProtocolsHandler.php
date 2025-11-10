<?php

declare(strict_types=1);

namespace Marvin\Protocol\Application\QueryHandler;

use EnderLab\DddCqrsBundle\Application\Query\QueryHandlerInterface;
use Marvin\Protocol\Application\Query\ListProtocols;
use Marvin\Protocol\Domain\Model\Protocol;
use Marvin\Protocol\Domain\Repository\ProtocolRepositoryInterface;
use Marvin\Protocol\Domain\ValueObject\ProtocolStatus;
use Marvin\Protocol\Domain\ValueObject\TransportType;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ListProtocolsHandler implements QueryHandlerInterface
{
    public function __construct(
        private ProtocolRepositoryInterface $protocolRepository,
    ) {
    }

    /**
     * @return array<Protocol>
     */
    public function __invoke(ListProtocols $query): array
    {
        $criteria = [];

        if ($query->type !== null) {
            $criteria['transportType'] = TransportType::from($query->type);
        }

        if ($query->status !== null) {
            $criteria['status'] = ProtocolStatus::from($query->status);
        }

        return $this->protocolRepository->byCriteria($criteria);
    }
}
