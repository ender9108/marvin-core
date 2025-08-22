<?php

namespace EnderLab\LockableBundle\Service;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\PessimisticLockException;

class DoctrineLockService
{
    private array $locks = [];

    public function __construct(
        private readonly EntityManagerInterface $em
    ) {}

    /**
     * @throws OptimisticLockException
     * @throws PessimisticLockException|ORMException
     */
    public function lock($entity, $lockMode, $refresh = true): object
    {
        $objectId = spl_object_id($entity);
        switch ($lockMode) {
            case LockMode::PESSIMISTIC_READ: // Si un lock READ ou WRITE a déjà été posé, pas besoin d'en refaire un
                if (isset($this->locks[LockMode::PESSIMISTIC_READ][$objectId])) {
                    return $entity;
                }
                if (isset($this->locks[LockMode::PESSIMISTIC_WRITE][$objectId])) {
                    return $entity;
                }
                break;
            case LockMode::PESSIMISTIC_WRITE: // Si un lock WRITE a déjà été posé, pas besoin d'en refaire un
                if (isset($this->locks[LockMode::PESSIMISTIC_WRITE][$objectId])) {
                    return $entity;
                }
                if (isset($this->locks[LockMode::PESSIMISTIC_READ][$objectId])) {
                    $this->em->lock($entity, $lockMode); // Si on a déjà un verrou PESSIMISTIC_READ, pas besoin de refresh
                    $this->locks[$lockMode][$objectId] = true;
                    return $entity;
                }
                break;
        }
        $refresh ? $this->em->refresh($entity, $lockMode) : $this->em->lock($entity, $lockMode);
        $this->locks[$lockMode][$objectId] = true;

        return $entity;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function find(string $className, int|string $id, $lockMode): ?object
    {
        $entity = $this->em->find($className, $id, $lockMode);
        if (null === $entity) {
            return null;
        }
        $objectId = spl_object_id($entity);
        $this->locks[$lockMode][$objectId] = true;

        return $entity;
    }

    public function clear(): void
    {
        $this->locks = [];
    }
}
