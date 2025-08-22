<?php

namespace EnderLab\BlameableBundle\Trait;

use EnderLab\DddCqrsBundle\ApiPlatform\ApiResourceInterface;

trait ApiMapperBlameableTrait
{
    public function setBlameableToResource(ApiResourceInterface $dto, object $entity): ApiResourceInterface
    {
        if (
            property_exists($dto, 'createdBy') &&
            property_exists($dto, 'updatedBy') &&
            method_exists($entity, 'getCreatedBy') &&
            method_exists($entity, 'getUpdatedBy')
        ) {
            $dto->createdBy = $entity->getCreatedBy();
            $dto->updatedBy = $entity->getUpdatedBy();
        }

        return $dto;
    }
}
