<?php

namespace EnderLab\LoggerBundle\Request;

use Symfony\Component\Uid\Uuid;

class RequestUuidGenerator
{
    public function generate(): string
    {
        return Uuid::v4()->toRfc4122();
    }
}
