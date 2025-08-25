<?php

namespace App\Domotic\Domain\Model;

use Doctrine\ORM\Mapping as ORM;
use EnderLab\BlameableBundle\Interface\BlameableInterface;
use EnderLab\BlameableBundle\Trait\BlameableTrait;
use EnderLab\TimestampableBundle\Interface\TimestampableInterface;
use EnderLab\TimestampableBundle\Trait\TimestampableTrait;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;

#[ORM\Entity]
#[ORM\Cache(usage: 'READ_ONLY', region: 'read_only')]
class ProtocolStatus implements TimestampableInterface, BlameableInterface
{
    use BlameableTrait;
    use TimestampableTrait;

    public const string STATUS_ENABLED = 'enabled';
    public const string STATUS_DISABLED = 'disabled';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[Orm\GeneratedValue(strategy: 'CUSTOM')]
    #[Orm\CustomIdGenerator(class: UuidGenerator::class)]
    private ?string $id = null;

    #[ORM\Column(length: 128)]
    private ?string $label = null;

    #[ORM\Column(length: 64)]
    private ?string $reference = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
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
}
