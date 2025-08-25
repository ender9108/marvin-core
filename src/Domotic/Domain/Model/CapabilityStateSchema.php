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
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class CapabilityStateSchema extends AggregateRoot implements TimestampableInterface, BlameableInterface
{
    use BlameableTrait;
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[Orm\GeneratedValue(strategy: 'CUSTOM')]
    #[Orm\CustomIdGenerator(class: UuidGenerator::class)]
    private ?string $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotNull()]
    #[Assert\Length(max: 255)]
    private ?string $label = null;

    #[ORM\Column(type: 'string', length: 128)]
    #[Assert\NotNull()]
    #[Assert\Length(max: 128)]
    private ?string $reference = null;

    /**
     * @var Collection<int, CapabilityAction>
     */
    #[ORM\ManyToMany(targetEntity: CapabilityAction::class, inversedBy: 'capabilities')]
    private Collection $capabilityActions;

    public function __construct()
    {
        $this->capabilityActions = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): void
    {
        $this->label = $label;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): void
    {
        $this->reference = $reference;
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
}
