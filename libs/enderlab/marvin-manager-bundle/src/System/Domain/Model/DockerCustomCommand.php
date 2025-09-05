<?php
namespace EnderLab\MarvinManagerBundle\System\Domain\Model;

use EnderLab\MarvinManagerBundle\System\Domain\ValueObject\Identity\DockerCustomCommandId;
use Marvin\Shared\Domain\ValueObject\Reference;

class DockerCustomCommand
{
    private ?DockerCustomCommandId $id = null;

    private ?string $reference = null;

    private ?string $command = null;

    private ?Docker $docker;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getCommand(): ?string
    {
        return $this->command;
    }

    public function setCommand(string $command): static
    {
        $this->command = $command;

        return $this;
    }

    public function getDocker(): ?Docker
    {
        return $this->docker;
    }

    public function setDocker(?Docker $docker): static
    {
        $this->docker = $docker;

        return $this;
    }
}
