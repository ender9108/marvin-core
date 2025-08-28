<?php

namespace App\System\Domain\Model;

use App\System\Domain\Event\Plugin\PluginCreated;
use App\System\Domain\Event\Plugin\PluginDeleted;
use App\System\Domain\Event\Plugin\PluginDisabled;
use App\System\Domain\Event\Plugin\PluginEnabled;
use Doctrine\ORM\Mapping as ORM;
use EnderLab\BlameableBundle\Interface\BlameableInterface;
use EnderLab\BlameableBundle\Trait\BlameableTrait;
use EnderLab\DddCqrsBundle\Domain\Aggregate\AggregateRoot;
use EnderLab\TimestampableBundle\Interface\TimestampableInterface;
use EnderLab\TimestampableBundle\Trait\TimestampableTrait;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\UuidV4;

#[ORM\Entity]
class Plugin extends AggregateRoot implements TimestampableInterface, BlameableInterface
{
    use BlameableTrait;
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private ?string $id = null;

    #[ORM\Column(length: 128)]
    private ?string $label = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'string', length: 64, unique: true, nullable: true)]
    private ?string $reference = null;

    #[ORM\Column(type: 'string', length: 8, nullable: true)]
    private ?string $version = null;

    #[ORM\ManyToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?PluginStatus $status = null;

    public function __construct() {
        $this->id = (string) new UuidV4();
        $this->recordThat(new PluginCreated($this->id));
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(?string $version): static
    {
        $this->version = $version;

        return $this;
    }

    public function getStatus(): ?PluginStatus
    {
        return $this->status;
    }

    public function setStatus(PluginStatus $status): static
    {
        $this->sendEventByStatus($status);
        $this->status = $status;
        return $this;
    }

    private function sendEventByStatus(PluginStatus $status): void
    {
        if ($this->status !== $status) {
            match ($status->getReference()) {
                PluginStatus::STATUS_DISABLED => $this->recordThat(new PluginDisabled($this->id)),
                PluginStatus::STATUS_TO_DELETE => $this->recordThat(new PluginDeleted($this->id)),
                PluginStatus::STATUS_ENABLED => $this->recordThat(new PluginEnabled($this->id)),
                default => null,
            };
        }
    }
}
