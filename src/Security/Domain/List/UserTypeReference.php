<?php

namespace Marvin\Security\Domain\List;

enum UserTypeReference: string
{
    case TYPE_APPLICATION = 'app';
    case TYPE_CLI = 'cli';
    case TYPE_SYSTEM = 'system';
}
