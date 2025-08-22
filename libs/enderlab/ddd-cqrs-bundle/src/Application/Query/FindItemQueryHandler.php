<?php

namespace EnderLab\DddCqrsBundle\Application\Query;

use Doctrine\ORM\EntityManagerInterface;
use EnderLab\DddCqrsBundle\Application\Exception\MissingEntityException;
use EnderLab\DddCqrsBundle\Application\Query\Attribute\AsQueryHandler;

#[AsQueryHandler]
readonly class FindItemQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    /**
     * @throws MissingEntityException
     */
    public function __invoke(FindItemQuery $query): object
    {
        $entity = $this->em->getRepository($query->className)->find($query->id);

        if (!$entity) {
            throw new MissingEntityException($query->id, $query->className);
        }

        return $entity;
    }
}
