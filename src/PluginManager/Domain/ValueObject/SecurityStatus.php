<?php

namespace Marvin\PluginManager\Domain\ValueObject;

enum SecurityStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case NEEDS_REVIEW = 'needs_review';
}
