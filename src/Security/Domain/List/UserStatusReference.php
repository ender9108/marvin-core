<?php

namespace Marvin\Security\Domain\List;

enum UserStatusReference: string
{
    case STATUS_ENABLED = 'enabled';
    case STATUS_DISABLED = 'disabled';
    case STATUS_TO_DELETE = 'to_delete';
    case STATUS_LOCKED = 'locked';
}
