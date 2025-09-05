<?php
namespace EnderLab\MarvinManagerBundle\System\Domain\Model;

use EnderLab\DddCqrsBundle\Domain\Model\AggregateRoot;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use EnderLab\MarvinManagerBundle\System\Domain\Event\Docker\DockerCreated;
use EnderLab\MarvinManagerBundle\System\Domain\ValueObject\Identity\DockerId;

class Docker extends AggregateRoot
{
    public readonly DockerId $id;

    public function __construct(
        private(set) ?string $containerId,
        private(set) ?string $containerName,
        private(set) Collection $dockerCustomCommands,
        private(set) ?string $containerImage,
        private(set) ?string $containerService,
        private(set) ?string $containerState,
        private(set) ?string $containerStatus,
        private(set) ?string $containerProject,
        private(set) array $definition = [],
    ) {
        $this->id = new DockerId();
        $this->dockerCustomCommands = new ArrayCollection();
        $this->recordThat(new DockerCreated($this->id));
    }

    public function updateContainerId(string $containerId): self
    {
        $this->containerId = $containerId;
    }

    public function updateContainerName(string $containerName): self
    {
        $this->containerName = $containerName;
    }

    public function updateContainerInfos(
        ?string $containerImage = null,
        ?string $containerService = null,
        ?string $containerState = null,
        ?string $containerStatus = null,
        ?string $containerProject = null,
        array $definition = [],
    ): self {
        $this->containerImage = $containerImage ?? $this->containerImage;
        $this->containerService = $containerService ?? $this->containerService;
        $this->containerState = $containerState ?? $this->containerState;
        $this->containerStatus = $containerStatus ?? $this->containerStatus;
        $this->containerProject = $containerProject ?? $this->containerProject;
        $this->definition = empty($definition) ? $this->definition : $definition;

        return $this;
    }

    public function addDockerCustomCommand(DockerCustomCommand $dockerCustomCommand): static
    {
        if (!$this->dockerCustomCommands->contains($dockerCustomCommand)) {
            $this->dockerCustomCommands->add($dockerCustomCommand);
            $dockerCustomCommand->setDocker($this);
        }

        return $this;
    }

    public function removeDockerCustomCommand(DockerCustomCommand $dockerCustomCommand): static
    {
        if ($this->dockerCustomCommands->removeElement($dockerCustomCommand)) {
            // set the owning side to null (unless already changed)
            if ($dockerCustomCommand->getDocker() === $this) {
                $dockerCustomCommand->setDocker(null);
            }
        }

        return $this;
    }
}
