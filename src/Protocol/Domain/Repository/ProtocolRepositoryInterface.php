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

namespace Marvin\Protocol\Domain\Repository;

use Marvin\Protocol\Domain\Model\Protocol;
use Marvin\Protocol\Domain\ValueObject\ProtocolStatus;
use Marvin\Protocol\Domain\ValueObject\TransportType;
use Marvin\Shared\Domain\ValueObject\Identity\ProtocolId;
use Marvin\Shared\Domain\ValueObject\Label;

interface ProtocolRepositoryInterface
{
    public function save(Protocol $protocol, bool $flush = true): void;

    public function remove(Protocol $protocol, bool $flush = true): void;

    /**
     * @return Protocol[]
     */
    public function all(): array;

    public function byId(ProtocolId $id): Protocol;

    public function byName(Label $name): ?Protocol;

    public function byTransportType(TransportType $type): array;

    public function byStatus(ProtocolStatus $status): array;

    public function exists(ProtocolId $id): bool;
}
