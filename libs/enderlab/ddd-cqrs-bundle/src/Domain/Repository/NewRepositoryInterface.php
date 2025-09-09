<?php

namespace EnderLab\DddCqrsBundle\Domain\Repository;

use Countable;
use Iterator;
use IteratorAggregate;

interface NewRepositoryInterface extends IteratorAggregate, Countable
{
    public function getIterator(): Iterator;

    public function count(): int;

    public function paginator(): ?PaginatorInterface;

    public function withPagination(int $page, int $itemsPerPage): static;

    public function withoutPagination(): static;
}
