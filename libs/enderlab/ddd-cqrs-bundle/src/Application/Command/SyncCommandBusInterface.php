<?php

declare(strict_types=1);

namespace EnderLab\DddCqrsBundle\Application\Command;

interface SyncCommandBusInterface
{
    public function handle(SyncCommandInterface $message): mixed;
}
