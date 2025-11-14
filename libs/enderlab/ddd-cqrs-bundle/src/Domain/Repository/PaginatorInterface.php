<?php

declare(strict_types=1);

namespace EnderLab\DddCqrsBundle\Domain\Repository;

use Countable;
use IteratorAggregate;

interface PaginatorInterface extends IteratorAggregate, Countable
{
    public function getCurrentPage(): int;
    public function getItemsPerPage(): int;
    public function getLastPage(): int;
    public function getTotalItems(): int;
}
