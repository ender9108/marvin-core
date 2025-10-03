<?php

namespace Marvin\Domotic\Domain\List;

enum ProtocolStatusReference: string
{
    case STATUS_ENABLED = 'enabled';
    case STATUS_DISABLED = 'disabled';
}
