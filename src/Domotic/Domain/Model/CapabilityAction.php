<?php

namespace App\Domotic\Domain\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use EnderLab\BlameableBundle\Interface\BlameableInterface;
use EnderLab\BlameableBundle\Trait\BlameableTrait;
use EnderLab\TimestampableBundle\Interface\TimestampableInterface;
use EnderLab\TimestampableBundle\Trait\TimestampableTrait;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class CapabilityAction implements TimestampableInterface, BlameableInterface
{
    use BlameableTrait;
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'smallint', options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotNull()]
    #[Assert\Length(max: 255)]
    private ?string $label = null;

    #[ORM\Column(type: 'string', length: 128)]
    #[Assert\NotNull()]
    #[Assert\Length(max: 128)]
    private ?string $reference = null;

    /**
     * @var Collection<int, Capability>
     */
    #[ORM\ManyToMany(targetEntity: Capability::class, mappedBy: 'capabilityActions')]
    private Collection $capabilities;

    public function __construct()
    {
        $this->capabilities = new ArrayCollection();
    }

    public function getId(): ?int
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
     * @return Collection<int, Capability>
     */
    public function getCapabilities(): Collection
    {
        return $this->capabilities;
    }

    public function addCapability(Capability $capability): static
    {
        if (!$this->capabilities->contains($capability)) {
            $this->capabilities->add($capability);
            $capability->addCapabilityAction($this);
        }

        return $this;
    }

    public function removeCapability(Capability $capability): static
    {
        if ($this->capabilities->removeElement($capability)) {
            $capability->removeCapabilityAction($this);
        }

        return $this;
    }
}
