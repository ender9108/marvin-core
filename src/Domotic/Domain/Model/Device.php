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
     * @var Collection<int, Capability>
     */
    #[ORM\ManyToMany(targetEntity: Capability::class)]
    private Collection $capabilities;

    public function __construct()
    {
        $this->id = (string) new UuidV4();
        $this->capabilities = new ArrayCollection();
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
        }

        return $this;
    }

    public function removeCapability(Capability $capability): static
    {
        $this->capabilities->removeElement($capability);

        return $this;
    }
}
