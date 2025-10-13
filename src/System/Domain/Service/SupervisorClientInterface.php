<?php

namespace Marvin\System\Domain\Service;

interface SupervisorClientInterface
{
    public function listProcesses(): array;

    public function start(string $name): mixed;

    public function stop(string $name): mixed;

    public function restart(string $name): void;

    public function reloadConfig(): array;

    public function version(): string;

    public function getState(): array;
}
