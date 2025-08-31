<?php
namespace EnderLab\MarvinManagerBundle\System\Domain\Model;

use EnderLab\DddCqrsBundle\Domain\Aggregate\AggregateRoot;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use EnderLab\BlameableBundle\Interface\BlameableInterface;
use EnderLab\BlameableBundle\Trait\BlameableTrait;
use EnderLab\TimestampableBundle\Interface\TimestampableInterface;
use EnderLab\TimestampableBundle\Trait\TimestampableTrait;
use Symfony\Component\Uid\UuidV4;

#[ORM\Entity]
class Docker extends AggregateRoot implements BlameableInterface, TimestampableInterface
{
    use TimestampableTrait;
    use BlameableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private ?string $id = null;

    #[ORM\Column(length: 255)]
    private ?string $containerId = null;

    #[ORM\Column(length: 255)]
    private ?string $containerName = null;

    #[ORM\OneToMany(targetEntity: DockerCustomCommand::class, mappedBy: 'docker', cascade: ['persist', 'remove'])]
    private Collection $dockerCustomCommands;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $containerImage = null;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $containerService = null;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $containerState = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $containerStatus = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $containerProject = null;

    #[ORM\Column(type: 'json')]
    private array $definition = [];

    public function __construct()
    {
        $this->id = (string) new UuidV4();
        $this->dockerCustomCommands = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getContainerId(): ?string
    {
        return $this->containerId;
    }

    public function setContainerId(string $containerId): static
    {
        $this->containerId = $containerId;

        return $this;
    }

    public function getContainerName(): ?string
    {
        return $this->containerName;
    }

    public function setContainerName(string $containerName): static
    {
        $this->containerName = $containerName;

        return $this;
    }

    /**
     * @return Collection<int, DockerCustomCommand>
     */
    public function getDockerCustomCommands(): Collection
    {
        return $this->dockerCustomCommands;
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

    public function getContainerImage(): ?string
    {
        return $this->containerImage;
    }

    public function setContainerImage(?string $containerImage): static
    {
        $this->containerImage = $containerImage;

        return $this;
    }

    public function getContainerService(): ?string
    {
        return $this->containerService;
    }

    public function setContainerService(?string $containerService): static
    {
        $this->containerService = $containerService;

        return $this;
    }

    public function getContainerState(): ?string
    {
        return $this->containerState;
    }

    public function setContainerState(?string $containerState): static
    {
        $this->containerState = $containerState;

        return $this;
    }

    public function getContainerStatus(): ?string
    {
        return $this->containerStatus;
    }

    public function setContainerStatus(?string $containerStatus): static
    {
        $this->containerStatus = $containerStatus;

        return $this;
    }

    public function getContainerProject(): ?string
    {
        return $this->containerProject;
    }

    public function setContainerProject(?string $containerProject): static
    {
        $this->containerProject = $containerProject;

        return $this;
    }

    public function getDefinition(): array
    {
        return $this->definition;
    }

    public function setDefinition(array $definition): static
    {
        $this->definition = $definition;
        return $this;
    }
}
