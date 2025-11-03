<?php

namespace EnderLab\MarvinManagerBundle\Messenger\Bus;

use EnderLab\MarvinManagerBundle\Messenger\Response\ManagerResponseCommandInterface;

interface ManagerToMarvinCommandBusInterface
{
    public function dispatch(ManagerResponseCommandInterface $command): void;
}
