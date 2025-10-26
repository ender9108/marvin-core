<?php

namespace Marvin\Secret\Domain\ValueObject;

use DateMalformedStringException;
use DateTimeImmutable;

final readonly class RotationPolicy
{
    private function __construct(
        private SecretRotationManagement $management,
        private int $rotationIntervalDays,
        private bool $autoRotate,
        private ?string $rotationCommand = null,
    ) {
        if ($rotationIntervalDays < 0) {
            /** @todo */
            //throw new \InvalidArgumentException('Rotation interval cannot be negative');
        }

        if ($autoRotate && $rotationIntervalDays === 0) {
            /** @todo */
            //throw new \InvalidArgumentException('Auto-rotate requires a rotation interval > 0');
        }

        if ($autoRotate && !$management->canAutoRotate()) {
            /** @todo */
            /*throw new \InvalidArgumentException(
                'Auto-rotation is only allowed for managed secrets'
            );*/
        }

        if ($rotationIntervalDays < 0) {
            /** @todo */
            //throw new \InvalidArgumentException('Rotation interval cannot be negative');
        }

        if ($autoRotate && $rotationIntervalDays === 0) {
            /** @todo */
            /*throw new \InvalidArgumentException(
                'Auto-rotate requires a rotation interval > 0'
            );*/
        }
    }

    public static function create(
        SecretRotationManagement $management,
        int $rotationIntervalDays,
        bool $autoRotate,
        ?string $rotationCommand = null,
    ): self {
        return new self($management, $rotationIntervalDays, $autoRotate, $rotationCommand);
    }

    public static function managed(int $rotationIntervalDays, ?string $command = null): self
    {
        return new self(
            management: SecretRotationManagement::MANAGED,
            rotationIntervalDays: $rotationIntervalDays,
            autoRotate: true,
            rotationCommand: $command,
        );
    }

    public static function external(?int $expirationWarningDays = null): self
    {
        return new self(
            management: SecretRotationManagement::EXTERNAL,
            rotationIntervalDays: $expirationWarningDays ?? 0,
            autoRotate: false,
            rotationCommand: null,
        );
    }

    public static function managedNoRotation(): self
    {
        return new self(
            management: SecretRotationManagement::MANAGED,
            rotationIntervalDays: 0,
            autoRotate: false,
            rotationCommand: null,
        );
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
        return $nextRotation <= new DateTimeImmutable();
    }

    /**
     * @throws DateMalformedStringException
     */
    public function needsExpirationWarning(DateTimeImmutable $lastRotatedAt): bool
    {
        // Pour les secrets externes, alerter si ancien
        if (!$this->management->isExternal() || $this->rotationIntervalDays === 0) {
            return false;
        }

        $warningDate = $lastRotatedAt->modify("+{$this->rotationIntervalDays} days");
        return $warningDate <= new DateTimeImmutable();
    }

    public function getManagement(): SecretRotationManagement
    {
        return $this->management;
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
