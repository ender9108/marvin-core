<?php

namespace Marvin\Shared\Infrastructure\Persistence\Doctrine\DBAL\Types;

use Override;
use Marvin\Shared\Domain\ValueObject\Identity\SecurityReportId;
use Symfony\Bridge\Doctrine\Types\AbstractUidType;

final class SecurityReportIdType extends AbstractUidType
{
    #[Override]
    public function getName(): string
    {
        return 'security_report_id';
    }

    #[Override]
    protected function getUidClass(): string
    {
        return SecurityReportId::class;
    }
}
