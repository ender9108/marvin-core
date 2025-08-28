<?php

namespace App\Domotic\Domain\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use EnderLab\BlameableBundle\Interface\BlameableInterface;
use EnderLab\BlameableBundle\Trait\BlameableTrait;
use EnderLab\TimestampableBundle\Interface\TimestampableInterface;
use EnderLab\TimestampableBundle\Trait\TimestampableTrait;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;

#[ORM\Entity]
class CapabilityComposition implements TimestampableInterface, BlameableInterface
{
    use BlameableTrait;
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[Orm\GeneratedValue(strategy: 'CUSTOM')]
    #[Orm\CustomIdGenerator(class: UuidGenerator::class)]
    private ?string $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(unique: true, nullable: false)]
    private ?Capability $capability = null;

    /**
     * @var Collection<int, CapabilityAction>
     */
    #[ORM\ManyToMany(targetEntity: CapabilityAction::class)]
    private Collection $capabilityActions;

    /**
     * @var Collection<int, CapabilityState>
     */
    #[ORM\ManyToMany(targetEntity: CapabilityState::class)]
    private Collection $capabilityStates;

    public function __construct()
    {
        $this->capabilityActions = new ArrayCollection();
        $this->capabilityStates = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getCapability(): ?Capability
    {
        return $this->capability;
    }

    public function setCapability(?Capability $capability): static
    {
        $this->capability = $capability;

        return $this;
    }

    /**
     * @return Collection<int, CapabilityAction>
     */
    public function getCapabilityActions(): Collection
    {
        return $this->capabilityActions;
    }

    public function addCapabilityAction(CapabilityAction $capabilityAction): static
    {
        if (!$this->capabilityActions->contains($capabilityAction)) {
            $this->capabilityActions->add($capabilityAction);
        }

        return $this;
    }

    public function removeCapabilityAction(CapabilityAction $capabilityAction): static
    {
        $this->capabilityActions->removeElement($capabilityAction);

        return $this;
    }

    /**
     * @return Collection<int, CapabilityState>
     */
    public function getCapabilityStates(): Collection
    {
        return $this->capabilityStates;
    }

    public function addCapabilityState(CapabilityState $capabilityState): static
    {
        if (!$this->capabilityStates->contains($capabilityState)) {
            $this->capabilityStates->add($capabilityState);
        }

        return $this;
    }

    public function removeCapabilityState(CapabilityState $capabilityState): static
    {
        $this->capabilityStates->removeElement($capabilityState);

        return $this;
    }
}
