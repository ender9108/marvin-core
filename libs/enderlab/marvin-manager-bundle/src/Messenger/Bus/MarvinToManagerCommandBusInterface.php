<?php

declare(strict_types=1);

namespace EnderLab\MarvinManagerBundle\Messenger\Bus;

use EnderLab\MarvinManagerBundle\Messenger\Request\ManagerRequestCommandInterface;

interface MarvinToManagerCommandBusInterface
{
    public function dispatch(ManagerRequestCommandInterface $command): void;
}
