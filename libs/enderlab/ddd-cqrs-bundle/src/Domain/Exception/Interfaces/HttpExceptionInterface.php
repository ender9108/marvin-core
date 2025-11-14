<?php

declare(strict_types=1);

namespace EnderLab\DddCqrsBundle\Domain\Exception\Interfaces;

use Throwable;

interface HttpExceptionInterface extends Throwable
{
    public function getStatusCode(): int;
    public function getInternalCode(): string;
}
