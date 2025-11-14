<?php

declare(strict_types=1);

namespace EnderLab\DddCqrsBundle\Application\Query;

interface QueryBusInterface
{
    public function handle(QueryInterface $message): mixed;
}
