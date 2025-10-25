<?php

namespace Marvin\Secret\Domain\ValueObject;

enum SecretScope: string
{
    case GLOBAL = 'global';      // Accessible à toute l'app
    case USER = 'user';          // Spécifique à un user
    case DEVICE = 'device';      // Spécifique à un device
    case PROTOCOL = 'protocol';  // Spécifique à un protocole
}
