<?php

namespace Marvin\PluginManager\Domain\Repository;

use Marvin\PluginManager\Domain\Model\SecurityReport;
use Marvin\Shared\Domain\ValueObject\Identity\PluginId;
use Marvin\Shared\Domain\ValueObject\Identity\SecurityReportId;

interface SecurityReportRepositoryInterface
{
    public function save(SecurityReport $report): void;
    public function findById(SecurityReportId $id): ?SecurityReport;
    public function findLatestByPluginId(PluginId $pluginId): ?SecurityReport;
    public function findAllByPluginId(PluginId $pluginId): array;
}
