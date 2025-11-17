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
