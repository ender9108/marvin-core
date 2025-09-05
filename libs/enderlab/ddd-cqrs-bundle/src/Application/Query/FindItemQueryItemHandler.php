<?php

namespace EnderLab\DddCqrsBundle\Application\Query;

use Doctrine\ORM\EntityManagerInterface;
use EnderLab\DddCqrsBundle\Application\Exception\MissingModelException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
readonly class FindItemQueryItemHandler implements QueryHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    /**
     * @throws MissingModelException
     */
    public function __invoke(FindItemQuery $query): object
    {
        $entity = $this->em->getRepository($query->className)->find($query->id);

        if (!$entity) {
            throw new MissingModelException($query->id, $query->className);
        }

        return $entity;
    }
}
