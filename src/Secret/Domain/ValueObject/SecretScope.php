<?php

namespace Marvin\Secret\Domain\ValueObject;

use EnderLab\DddCqrsBundle\Domain\ValueObject\ValueObjectInterface;
use EnderLab\ToolsBundle\Service\EnumToArrayTrait;

enum SecretScope: string implements ValueObjectInterface
{
    use EnumToArrayTrait;

    case GLOBAL = 'global';      // Accessible à toute l'app
    case USER = 'user';          // Spécifique à un user
    case DEVICE = 'device';      // Spécifique à un device
    case PROTOCOL = 'protocol';  // Spécifique à un protocole

    public function equals(ValueObjectInterface $other): bool
    {
        return $this->value === $other->value;
    }
}
