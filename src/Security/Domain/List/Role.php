<?php
namespace Marvin\Security\Domain\List;

use EnderLab\ToolsBundle\Service\EnumToArrayTrait;

enum Role: string
{
    use EnumToArrayTrait;

    case USER = 'ROLE_USER';
    case ADMIN = 'ROLE_ADMIN';
    case SUPER_ADMIN = 'ROLE_SUPER_ADMIN';
}
