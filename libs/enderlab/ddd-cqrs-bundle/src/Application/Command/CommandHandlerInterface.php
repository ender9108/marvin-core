<?php

namespace EnderLab\DddCqrsBundle\Application\Command;

interface CommandHandlerInterface
{
    public function __invoke(CommandInterface $command): void;
}
