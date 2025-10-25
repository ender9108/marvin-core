<?php

namespace Marvin\Secret\Domain\ValueObject;

use DateMalformedStringException;
use Marvin\Shared\Domain\ValueObject\ArrayValueObjectInterface;

final readonly class RotationPolicy implements ArrayValueObjectInterface
{
    private function __construct(
        private int $rotationIntervalDays,
        private bool $autoRotate,
        private ?string $rotationCommand = null,
    ) {
        if ($rotationIntervalDays < 0) {
            throw new \InvalidArgumentException('Rotation interval cannot be negative');
        }

        if ($autoRotate && $rotationIntervalDays === 0) {
            throw new \InvalidArgumentException('Auto-rotate requires a rotation interval > 0');
        }
    }

    public static function create(
        int $rotationIntervalDays,
        bool $autoRotate,
        ?string $rotationCommand = null,
    ): self {
        return new self($rotationIntervalDays, $autoRotate, $rotationCommand);
    }

    public static function never(): self
    {
        return new self(0, false, null);
    }

    public static function every(int $days, ?string $command = null): self
    {
        return new self($days, true, $command);
    }

    /**
     * @throws DateMalformedStringException
     */
    public function shouldRotate(\DateTimeInterface $lastRotatedAt): bool
    {
        if (!$this->autoRotate || $this->rotationIntervalDays === 0) {
            return false;
        }

        $nextRotation = $lastRotatedAt->modify("+{$this->rotationIntervalDays} days");
        return $nextRotation <= new \DateTimeImmutable();
    }

    public function getRotationIntervalDays(): int
    {
        return $this->rotationIntervalDays;
    }

    public function isAutoRotate(): bool
    {
        return $this->autoRotate;
    }

    public function getRotationCommand(): ?string
    {
        return $this->rotationCommand;
    }

    public function toArray(): array
    {
        return [
            'rotation_interval_days' => $this->rotationIntervalDays,
            'auto_rotate' => $this->autoRotate,
            'rotation_command' => $this->rotationCommand,
        ];
    }
}
