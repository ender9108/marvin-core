<?php

namespace Marvin\Protocol\Domain\Model;

use DateTimeImmutable;
use DateTimeInterface;
use EnderLab\DddCqrsBundle\Domain\Model\AggregateRoot;
use Marvin\Protocol\Domain\Event\ProtocolCommandSent;
use Marvin\Protocol\Domain\Event\ProtocolRegistered;
use Marvin\Protocol\Domain\Event\ProtocolStatusChanged;
use Marvin\Protocol\Domain\ValueObject\ExecutionMode;
use Marvin\Protocol\Domain\ValueObject\ProtocolConfiguration;
use Marvin\Protocol\Domain\ValueObject\ProtocolStatus;
use Marvin\Protocol\Domain\ValueObject\TransportType;
use Marvin\Shared\Domain\ValueObject\Identity\ProtocolId;
use Marvin\Shared\Domain\ValueObject\Label;

class Protocol extends AggregateRoot
{
    public function __construct(
        private(set) Label $name,
        private(set) TransportType $transportType,
        private(set) ProtocolConfiguration $configuration,
        private(set) ProtocolStatus $status,
        private(set) ExecutionMode $preferredExecutionMode,
        private(set) ?DateTimeInterface $updatedAt = null,
        private(set) DateTimeInterface $createdAt = new DateTimeImmutable(),
        private(set) ProtocolId $id = new ProtocolId(),
    ) {
    }

    public static function register(
        Label $name,
        TransportType $transportType,
        ProtocolConfiguration $configuration,
        ExecutionMode $preferredExecutionMode = ExecutionMode::DEVICE_LOCK,
    ): self {
        $protocol = new self(
            name: $name,
            transportType: $transportType,
            configuration: $configuration,
            status: ProtocolStatus::DISCONNECTED,
            preferredExecutionMode: $preferredExecutionMode,
        );

        $protocol->recordEvent(new ProtocolRegistered(
            protocolId: $protocol->id->toString(),
            name: $name->value,
            transportType: $transportType->value,
            configuration: $configuration->toArray(),
            preferredExecutionMode: $preferredExecutionMode->value,
        ));

        return $protocol;
    }

    public function updateConfiguration(ProtocolConfiguration $configuration): void
    {
        $this->configuration = $configuration;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function updatePreferredExecutionMode(ExecutionMode $mode): void
    {
        $this->preferredExecutionMode = $mode;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function connect(): void
    {
        $previousStatus = $this->status;
        $this->status = ProtocolStatus::CONNECTED;
        $this->updatedAt = new DateTimeImmutable();

        $this->recordEvent(new ProtocolStatusChanged(
            protocolId: $this->id->toString(),
            previousStatus: $previousStatus->value,
            newStatus: $this->status->value,
        ));
    }

    public function disconnect(): void
    {
        $previousStatus = $this->status;
        $this->status = ProtocolStatus::DISCONNECTED;
        $this->updatedAt = new DateTimeImmutable();

        $this->recordEvent(new ProtocolStatusChanged(
            protocolId: $this->id->toString(),
            previousStatus: $previousStatus->value,
            newStatus: $this->status->value,
        ));
    }

    public function markAsError(string $errorMessage): void
    {
        $previousStatus = $this->status;
        $this->status = ProtocolStatus::ERROR;
        $this->updatedAt = new DateTimeImmutable();

        $this->recordEvent(new ProtocolStatusChanged(
            protocolId: $this->id->toString(),
            previousStatus: $previousStatus->value,
            newStatus: $this->status->value,
            errorMessage: $errorMessage,
        ));
    }

    public function recordCommandSent(
        string $deviceId,
        string $action,
        array $parameters,
        ExecutionMode $executionMode,
        ?string $correlationId = null
    ): void {
        $this->recordEvent(new ProtocolCommandSent(
            protocolId: $this->id->toString(),
            deviceId: $deviceId,
            action: $action,
            parameters: $parameters,
            executionMode: $executionMode->value,
            correlationId: $correlationId,
        ));
    }

    public function supportsCorrelation(): bool
    {
        return $this->transportType->supportsCorrelation();
    }

    public function isConnected(): bool
    {
        return $this->status->isConnected();
    }

    public function isDisconnected(): bool
    {
        return $this->status->isDisconnected();
    }

    public function hasError(): bool
    {
        return $this->status->isError();
    }
}
