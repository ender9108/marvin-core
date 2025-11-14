<?php

declare(strict_types=1);

namespace EnderLab\DddCqrsBundle\Application\Command;

interface CommandBusInterface
{
    public function dispatch(CommandInterface $command): void;
}
