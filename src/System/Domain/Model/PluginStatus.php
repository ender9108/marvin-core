<?php

namespace App\System\Domain\Model;

use Doctrine\ORM\Mapping as ORM;
use EnderLab\BlameableBundle\Interface\BlameableInterface;
use EnderLab\BlameableBundle\Trait\BlameableTrait;
use EnderLab\DddCqrsApiPlatformBundle\Mapper\Attribute\AsTranslatableApiProperty;
use EnderLab\TimestampableBundle\Interface\TimestampableInterface;
use EnderLab\TimestampableBundle\Trait\TimestampableTrait;

#[ORM\Entity]
#[ORM\Cache(usage: 'READ_ONLY', region: 'read_only')]
class PluginStatus implements TimestampableInterface, BlameableInterface
{
    use BlameableTrait;
    use TimestampableTrait;

    public const string STATUS_ENABLED = 'enabled';
    public const string STATUS_DISABLED = 'disabled';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'smallint', options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(length: 128)]
    #[AsTranslatableApiProperty]
    private ?string $label = null;

    #[ORM\Column(type: 'string', length: 64, unique: true, nullable: true)]
    private ?string $reference = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): self
    {
        $this->label = $label;
        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }
}
