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

namespace Marvin\Device\Infrastructure\Framework\Symfony\Service\Acl;

use Marvin\Device\Application\Service\Acl\ProtocolQueryServiceInterface;
use Marvin\Protocol\Domain\Repository\ProtocolRepositoryInterface;
use Marvin\Shared\Domain\ValueObject\Identity\ProtocolId;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * ACL Service: Device → Protocol (queries)
 *
 * Allows Device Context to query Protocol Context without direct dependency
 */
final readonly class ProtocolQueryService implements ProtocolQueryServiceInterface
{
    public function __construct(
        private ProtocolRepositoryInterface $protocolRepository,
        private LoggerInterface $logger,
    ) {
    }

    public function protocolExists(ProtocolId $protocolId): bool
    {
        try {
            return $this->protocolRepository->exists($protocolId);
        } catch (Throwable $e) {
            $this->logger->error('Error checking if protocol exists', [
                'protocolId' => $protocolId->toString(),
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function isProtocolEnabled(ProtocolId $protocolId): bool
    {
        try {
            $protocol = $this->protocolRepository->byId($protocolId);

            return $protocol->isConnected();
        } catch (Throwable $e) {
            $this->logger->error('Error checking if protocol is enabled', [
                'protocolId' => $protocolId->toString(),
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function getProtocolStatus(ProtocolId $protocolId): string
    {
        try {
            $protocol = $this->protocolRepository->byId($protocolId);

            return $protocol->status->value;
        } catch (Throwable $e) {
            $this->logger->error('Error getting protocol status', [
                'protocolId' => $protocolId->toString(),
                'error' => $e->getMessage(),
            ]);

            return 'error';
        }
    }
}
