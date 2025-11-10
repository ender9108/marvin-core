<?php
namespace EnderLab\DddCqrsBundle\Domain\ValueObject;

trait ValueObjectTrait
{
    public function equals(self $other): bool
    {
        if (is_array($this->value)) {
            return count(array_diff($this->value, $other->value)) === 0;
        }

        return $this->value = $other->value;
    }
}
