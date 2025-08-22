<?php

namespace EnderLab\BlameableBundle\Trait;

use Doctrine\ORM\Mapping as ORM;

trait BlameableTrait
{
    #[ORM\Column(name: 'created_by', type: 'string', length: 255, nullable: true)]
    private ?string $createdBy = null;

    #[ORM\Column(name: 'updated_by', type: 'string', length: 255, nullable: true)]
    #[ORM\Embedded(columnPrefix: false)]
    private ?string $updatedBy = null;

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?string $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getUpdatedBy(): ?string
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?string $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }
}
