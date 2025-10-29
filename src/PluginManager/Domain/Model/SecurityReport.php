<?php

namespace Marvin\PluginManager\Domain\Model;

use DateTimeImmutable;
use Marvin\PluginManager\Domain\ValueObject\SecurityStatus;
use Marvin\Shared\Domain\ValueObject\Identity\SecurityReportId;

class SecurityReport
{
    public function __construct(
        private(set) Plugin $plugin,
        private(set) SecurityStatus $status,
        private(set) DateTimeImmutable $analyzedAt,
        private(set) string $analyzerVersion,
        private(set) array $violations,
        private(set) array $summary,
        private(set) SecurityReportId $id = new SecurityReportId(),
    ) {
    }

    public function hasViolations(): bool
    {
        return $this->status === SecurityStatus::REJECTED;
    }

    public function isApproved(): bool
    {
        return $this->status === SecurityStatus::APPROVED;
    }

    public function securityScore(): int
    {
        return $this->summary['security_score'] ?? 0;
    }
}
