<?php

declare(strict_types=1);

namespace EnderLab\MarvinManagerBundle\Messenger\Response;

interface ManagerResponseCommandInterface
{
    public function isSuccess(): bool;
    public function isFailed(): bool;
    public function hasError(): bool;
}
