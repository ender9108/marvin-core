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

namespace Marvin\Protocol\Infrastructure\Persistence\Doctrine\ORM;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Marvin\Protocol\Domain\Exception\ProtocolNotFoundException;
use Marvin\Protocol\Domain\Model\Protocol;
use Marvin\Protocol\Domain\Repository\ProtocolRepositoryInterface;
use Marvin\Protocol\Domain\ValueObject\ProtocolStatus;
use Marvin\Protocol\Domain\ValueObject\TransportType;
use Marvin\Shared\Domain\ValueObject\Identity\ProtocolId;
use Marvin\Shared\Domain\ValueObject\Label;

final class ProtocolOrmRepository extends ServiceEntityRepository implements ProtocolRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Protocol::class);
    }

    public function save(Protocol $protocol, bool $flush = true): void
    {
        $this->getEntityManager()->persist($protocol);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Protocol $protocol, bool $flush = true): void
    {
        $this->getEntityManager()->remove($protocol);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Protocol[]
     */
    public function all(): array
    {
        return parent::findAll();
    }

    public function byId(ProtocolId $id): Protocol
    {
        /** @var Protocol|null $protocol */
        $protocol = $this->findOneBy(['id' => $id->toString()]);

        if ($protocol === null) {
            throw ProtocolNotFoundException::withId($id);
        }

        return $protocol;
    }

    public function byName(Label $name): ?Protocol
    {
        return $this->createQueryBuilder('p')
            ->where('p.name.value = :name')
            ->setParameter('name', $name->value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function byTransportType(TransportType $type): array
    {
        return $this->findBy(['transportType' => $type]);
    }

    public function byStatus(ProtocolStatus $status): array
    {
        return $this->findBy(['status' => $status]);
    }

    /**
     * @param array<string, mixed> $criteria
     * @return Protocol[]
     */
    public function byCriteria(array $criteria): array
    {
        return $this->findBy($criteria);
    }

    public function exists(ProtocolId $id): bool
    {
        $count = $this
            ->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.id = :id')
            ->setParameter('id', $id->toString())
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return $count > 0;
    }
}
