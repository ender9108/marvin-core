<?php
namespace Marvin\System\Domain\Model;

use DateTimeImmutable;
use EnderLab\DddCqrsBundle\Domain\Model\AggregateRoot;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Marvin\Shared\Domain\ValueObject\CreatedAt;
use Marvin\Shared\Domain\ValueObject\UpdatedAt;
use Marvin\System\Domain\ValueObject\Identity\DockerId;

final class Docker extends AggregateRoot
{
    public readonly DockerId $id;

    public function __construct(
        private(set) ?string $containerId,
        private(set) ?string $containerName,
        private(set) Collection $dockerCommands,
        private(set) ?string $containerImage,
        private(set) ?string $containerService,
        private(set) ?string $containerState,
        private(set) ?string $containerStatus,
        private(set) ?string $containerProject,
        private(set) array $definition = [],
        private(set) ?UpdatedAt $updatedAt = null,
        public readonly CreatedAt $createdAt = new CreatedAt(new DateTimeImmutable())
    ) {
        $this->id = new DockerId();
        $this->dockerCommands = new ArrayCollection();
    }

    public function updateContainerInfos(
        ?string $containerId = null,
        ?string $containerName = null,
        ?string $containerImage = null,
        ?string $containerService = null,
        ?string $containerState = null,
        ?string $containerStatus = null,
        ?string $containerProject = null,
        array $definition = [],
    ): self {
        $this->containerId = $containerId ?? $this->containerId;
        $this->containerName = $containerName ?? $this->containerName;
        $this->containerImage = $containerImage ?? $this->containerImage;
        $this->containerService = $containerService ?? $this->containerService;
        $this->containerState = $containerState ?? $this->containerState;
        $this->containerStatus = $containerStatus ?? $this->containerStatus;
        $this->containerProject = $containerProject ?? $this->containerProject;
        $this->definition = empty($definition) ? $this->definition : $definition;

        return $this;
    }

    public function addDockerCustomCommand(DockerCommand $dockerCommand): static
    {
        if (!$this->dockerCommands->contains($dockerCommand)) {
            $this->dockerCommands->add($dockerCommand);
            $dockerCommand->docker = $this;
        }

        return $this;
    }

    public function removeDockerCommand(DockerCommand $dockerCommand): static
    {
        if ($this->dockerCommands->removeElement($dockerCommand)) {
            if ($dockerCommand->docker === $this) {
                $dockerCommand->docker = null;
            }
        }

        return $this;
    }
}
