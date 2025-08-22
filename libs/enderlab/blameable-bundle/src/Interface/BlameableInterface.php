<?php

namespace EnderLab\BlameableBundle\Interface;

interface BlameableInterface
{
    public function getId(): int|string|null;

    public function getCreatedBy(): ?string;

    public function setCreatedBy(?string $createdBy): self;

    public function getUpdatedBy(): ?string;

    public function setUpdatedBy(?string $updatedBy): self;
}
