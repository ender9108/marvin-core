<?php
namespace EnderLab\DddCqrsBundle\Domain\ValueObject;

trait ValueObjectTrait
{
    public function equals(self $other): bool
    {
        return $this->value = $other->value;
    }
}
