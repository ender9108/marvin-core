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
use Marvin\Protocol\Application\Query\GetProtocol;
use Marvin\Protocol\Domain\Model\Protocol;
use Marvin\Protocol\Domain\Repository\ProtocolRepositoryInterface;
use Marvin\Shared\Domain\ValueObject\Identity\ProtocolId;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetProtocolHandler implements QueryHandlerInterface
{
    public function __construct(
        private ProtocolRepositoryInterface $protocolRepository,
    ) {
    }

    public function __invoke(GetProtocol $query): Protocol
    {
        return $this->protocolRepository->byId(new ProtocolId($query->protocolId));
    }
}
