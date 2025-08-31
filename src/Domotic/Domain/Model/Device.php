<?php

namespace App\Domotic\Domain\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use EnderLab\BlameableBundle\Interface\BlameableInterface;
use EnderLab\BlameableBundle\Trait\BlameableTrait;
use EnderLab\DddCqrsBundle\Domain\Aggregate\AggregateRoot;
use EnderLab\TimestampableBundle\Interface\TimestampableInterface;
use EnderLab\TimestampableBundle\Trait\TimestampableTrait;
use Symfony\Component\Uid\UuidV4;

#[ORM\Entity]
class Device extends AggregateRoot implements TimestampableInterface, BlameableInterface
{
    use BlameableTrait;
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private ?string $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $technicalName = null;

    /**
     * @var Collection<int, CapabilityComposition>
     */
    #[ORM\ManyToMany(targetEntity: CapabilityComposition::class)]
    private Collection $capabilityCompositions;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Protocol $protocol = null;

    /**
     * @var Collection<int, Group>
     */
    #[ORM\ManyToMany(targetEntity: Group::class)]
    private Collection $groups;

    #[ORM\ManyToOne(inversedBy: 'devices')]
    private ?Zone $zone = null;

    public function __construct()
    {
        $this->id = (string) new UuidV4();
        $this->capabilityCompositions = new ArrayCollection();
        $this->groups = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getTechnicalName(): ?string
    {
        return $this->technicalName;
    }

    public function setTechnicalName(string $technicalName): static
    {
        $this->technicalName = $technicalName;

        return $this;
    }

    public function getCapabilityCompositionsByCapabilityReference(string $reference): Collection
    {
        return $this->capabilityCompositions->filter(
            function (CapabilityComposition $capabilityComposition) use ($reference) {
                return $capabilityComposition->getCapability()->getReference() === $reference;
            }
        );
    }

    /**
     * @return Collection<int, CapabilityComposition>
     */
    public function getCapabilityCompositions(): Collection
    {
        return $this->capabilityCompositions;
    }

    public function addCapabilityComposition(CapabilityComposition $capabilityComposition): static
    {
        if (!$this->capabilityCompositions->contains($capabilityComposition)) {
            $this->capabilityCompositions->add($capabilityComposition);
        }

        return $this;
    }

    public function removeCapabilityComposition(CapabilityComposition $capabilityComposition): static
    {
        $this->capabilityCompositions->removeElement($capabilityComposition);

        return $this;
    }

    public function getProtocol(): ?Protocol
    {
        return $this->protocol;
    }

    public function setProtocol(?Protocol $protocol): static
    {
        $this->protocol = $protocol;

        return $this;
    }

    /**
     * @return Collection<int, Group>
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(Group $group): static
    {
        if (!$this->groups->contains($group)) {
            $this->groups->add($group);
        }

        return $this;
    }

    public function removeGroup(Group $group): static
    {
        $this->groups->removeElement($group);

        return $this;
    }

    public function getZone(): ?Zone
    {
        return $this->zone;
    }

    public function setZone(?Zone $zone): static
    {
        $this->zone = $zone;

        return $this;
    }
}
