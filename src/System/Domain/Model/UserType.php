<?php

namespace App\System\Domain\Model;

use EnderLab\BlameableBundle\Interface\BlameableInterface;
use EnderLab\BlameableBundle\Trait\BlameableTrait;
use EnderLab\TimestampableBundle\Interface\TimestampableInterface;
use EnderLab\TimestampableBundle\Trait\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use EnderLab\ToolsBundle\Service\ListTrait;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;

#[ORM\Entity]
#[ORM\Table]
#[ORM\Cache(usage: 'READ_ONLY', region: 'read_only')]
class UserType implements TimestampableInterface, BlameableInterface
{
    use TimestampableTrait;
    use BlameableTrait;
    use ListTrait;

    public const string TYPE_APPLICATION = 'app';
    public const string TYPE_COMMAND = 'cmd';
    public const string TYPE_SYSTEM = 'system';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[Orm\GeneratedValue(strategy: 'CUSTOM')]
    #[Orm\CustomIdGenerator(class: UuidGenerator::class)]
    private ?string $id = null;

    #[ORM\Column(length: 128)]
    private ?string $label = null;

    #[ORM\Column(type: 'string', length: 64, unique: true, nullable: true)]
    private ?string $reference = null;

    public function getId(): ?string
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
