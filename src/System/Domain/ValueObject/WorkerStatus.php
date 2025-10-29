<?php

namespace Marvin\System\Domain\ValueObject;

use EnderLab\ToolsBundle\Service\EnumToArrayTrait;

enum WorkerStatus: string
{
    use EnumToArrayTrait;

    case STOPPED = 'stopped';    // Process arrêté
    case STARTING = 'starting';  // Process en cours de démarrage
    case RUNNING = 'running';   // Process en cours d'exécution (état sain)
    case BACKOFF = 'backoff';   // Process en mode backoff après échec démarrage
    case STOPPING = 'stopping';  // Process en cours d'arrêt
    case EXITED = 'exited';      // Process terminé (peut redémarrer si autorestart)
    case FATAL = 'fatal';        // Process en échec définitif (trop de tentatives)
    case UNKNOWN = 'unknown';    // État inconnu

    public function isHealthy(): bool
    {
        return $this === self::RUNNING;
    }

    public function isRunning(): bool
    {
        return $this === self::RUNNING;
    }

    public function isStopped(): bool
    {
        return $this === self::STOPPED;
    }

    public function isTransitional(): bool
    {
        return in_array($this, [self::STARTING, self::STOPPING], true);
    }

    public function isError(): bool
    {
        return in_array($this, [self::BACKOFF, self::FATAL], true);
    }

    public function canStart(): bool
    {
        return in_array($this, [self::STOPPED, self::EXITED, self::FATAL], true);
    }

    public function canStop(): bool
    {
        return in_array($this, [self::RUNNING, self::BACKOFF], true);
    }

    public function canRestart(): bool
    {
        return !in_array($this, [self::STARTING, self::STOPPING], true);
    }

    public function getColor(): string
    {
        return match ($this) {
            self::RUNNING => 'green',
            self::STARTING, self::STOPPING => 'yellow',
            self::STOPPED => 'gray',
            self::BACKOFF => 'red',
            self::FATAL => 'red',
            self::EXITED => 'cyan',
            self::UNKNOWN => 'magenta',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::RUNNING => '✓',
            self::STARTING => '⟳',
            self::STOPPING => '⏸',
            self::STOPPED => '■',
            self::BACKOFF => '⚠',
            self::FATAL => '✗',
            self::EXITED => '○',
            self::UNKNOWN => '?',
        };
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
