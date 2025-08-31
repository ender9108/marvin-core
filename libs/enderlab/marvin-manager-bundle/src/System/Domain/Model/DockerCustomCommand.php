<?php
namespace EnderLab\MarvinManagerBundle\System\Domain\Model;

use EnderLab\MarvinManagerBundle\Repository\DockerCustomCommandRepository;
use Doctrine\ORM\Mapping as ORM;
use EnderLab\BlameableBundle\Interface\BlameableInterface;
use EnderLab\BlameableBundle\Trait\BlameableTrait;
use EnderLab\TimestampableBundle\Interface\TimestampableInterface;
use EnderLab\TimestampableBundle\Trait\TimestampableTrait;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;

#[ORM\Entity(repositoryClass: DockerCustomCommandRepository::class)]
#[ORM\Table(
    uniqueConstraints: [new ORM\UniqueConstraint(
        name: 'docker_command_reference',
        columns: ['docker_id', 'reference'],
    )]
)]
class DockerCustomCommand implements BlameableInterface, TimestampableInterface
{
    use TimestampableTrait;
    use BlameableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[Orm\GeneratedValue(strategy: 'CUSTOM')]
    #[Orm\CustomIdGenerator(class: UuidGenerator::class)]
    private ?string $id = null;

    #[ORM\Column(length: 64)]
    private ?string $reference = null;

    #[ORM\Column(type: 'text')]
    private ?string $command = null;

    #[ORM\ManyToOne(inversedBy: 'dockerCommands')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Docker $docker;

    public function getId(): ?string
    {
        return $this->id;
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

    public function getCommand(): ?string
    {
        return $this->command;
    }

    public function setCommand(string $command): static
    {
        $this->command = $command;

        return $this;
    }

    public function getDocker(): ?Docker
    {
        return $this->docker;
    }

    public function setDocker(?Docker $docker): static
    {
        $this->docker = $docker;

        return $this;
    }
}
