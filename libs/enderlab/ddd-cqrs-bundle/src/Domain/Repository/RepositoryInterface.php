<?php

declare(strict_types=1);

namespace EnderLab\DddCqrsBundle\Domain\Repository;

interface RepositoryInterface
{
    public function byId(string|int $id);
}
