<?php

namespace EnderLab\LoggerBundle\Messenger\Stamp;

use Symfony\Component\Messenger\Stamp\StampInterface;

readonly class RequestIdStamp implements StampInterface
{
    public function __construct(
        private string $requestId
    ) {
    }

    public function getRequestId(): string
    {
        return $this->requestId;
    }
}
