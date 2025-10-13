<?php

namespace Marvin\System\Domain\Exception;

use EnderLab\DddCqrsBundle\Domain\Exception\DomainException;
use EnderLab\DddCqrsBundle\Domain\Exception\TranslatableExceptionInterface;
use Override;

final class SupervisorXmlRpcFault extends DomainException implements TranslatableExceptionInterface
{
    public function __construct(string $message) {
        parent::__construct($message);
        $this->code = 'SY0008';
    }

    #[Override]
    public function translationId(): string
    {
        return 'security.exceptions.supervisor_xml_rpc_fault';
    }

    #[Override]
    /** @return array<string, string> */
    public function translationParameters(): array
    {
        return [];
    }

    #[Override]
    public function translationDomain(): string
    {
        return 'system';
    }
}
