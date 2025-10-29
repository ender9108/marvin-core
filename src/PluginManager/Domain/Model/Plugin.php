<?php

namespace Marvin\PluginManager\Domain\Model;

use DateTimeImmutable;
use EnderLab\DddCqrsBundle\Domain\Assert\Assert;
use EnderLab\DddCqrsBundle\Domain\Model\AggregateRoot;
use Marvin\PluginManager\Domain\Event\PluginDisabled;
use Marvin\PluginManager\Domain\Event\PluginEnabled;
use Marvin\PluginManager\Domain\Event\PluginInstalled;
use Marvin\PluginManager\Domain\Event\PluginUninstalled;
use Marvin\PluginManager\Domain\Event\PluginUpdateBlocked;
use Marvin\PluginManager\Domain\Event\PluginUpdated;
use Marvin\PluginManager\Domain\ValueObject\PluginName;
use Marvin\PluginManager\Domain\ValueObject\PluginSlug;
use Marvin\PluginManager\Domain\ValueObject\PluginStatus;
use Marvin\PluginManager\Domain\ValueObject\PluginVersion;
use Marvin\Shared\Domain\ValueObject\Description;
use Marvin\Shared\Domain\ValueObject\Identity\PluginId;
use Marvin\Shared\Domain\ValueObject\Metadata;

class Plugin extends AggregateRoot
{
    public function __construct(
        private(set) PluginName $name,
        private(set) PluginSlug $slug,
        private(set) string $class,
        private(set) string $vendor,
        private(set) string $package,
        private(set) PluginVersion $version,
        private(set) PluginStatus $status,
        private(set) string $author,
        private(set) ?string $homepage,
        private(set) array $capabilities,
        private(set) DateTimeImmutable $installedAt,
        private(set) ?DateTimeImmutable $enabledAt = null,
        private(set) ?DateTimeImmutable $disabledAt = null,
        private(set) ?DateTimeImmutable $lastAnalyzedAt = null,
        private(set) ?PluginVersion $blockedVersion = null,
        private(set) ?DateTimeImmutable $blockedAt = null,
        private(set) ?Description $description = null,
        private(set) ?string $blockedReason = null,
        private(set) ?Metadata $metadata = null,
        private(set) PluginId $id = new PluginId(),
    ) {
    }

    public static function install(
        PluginName $name,
        PluginSlug $slug,
        string $class,
        string $vendor,
        string $package,
        PluginVersion $version,
        string $author,
        array $capabilities,
        ?string $homepage = null,
        ?Description $description = null,
        ?Metadata $metadata = null,
    ): self {
        $id = new PluginId();
        $plugin = new self(
            name: $name,
            slug: $slug,
            class: $class,
            vendor: $vendor,
            package: $package,
            version: $version,
            status: PluginStatus::INSTALLED,
            author: $author,
            homepage: $homepage,
            capabilities: $capabilities,
            installedAt: new \DateTimeImmutable(),
            description: $description,
            metadata: $metadata,
            id: $id,
        );

        $plugin->recordEvent(new PluginInstalled(
            pluginId: $id->toString(),
            pluginName: $name->value,
            pluginClass: $class,
            version: $version->value,
            capabilities: $capabilities,
        ));

        return $plugin;
    }

    public function enable(): void
    {
        Assert::eq($this->status, PluginStatus::DISABLED, 'PM0007::::plugin_is_already_enabled');
        Assert::eq($this->status, PluginStatus::ENABLED, 'PM0008::::plugin_update_is_blocked_no_enable');

        $this->status = PluginStatus::ENABLED;
        $this->enabledAt = new \DateTimeImmutable();
        $this->disabledAt = null;

        $this->recordEvent(new PluginEnabled(
            pluginId: $this->id->toString(),
            pluginName: $this->name->value,
            pluginClass: $this->class,
        ));
    }

    public function disable(?string $reason = null): void
    {
        Assert::eq($this->status, PluginStatus::DISABLED, 'PM0007::::plugin_is_already_disabled');
        Assert::eq($this->status, PluginStatus::ENABLED, 'PM0008::::plugin_only_enabled_plugins_can_be_disabled');

        $this->status = PluginStatus::DISABLED;
        $this->disabledAt = new \DateTimeImmutable();

        $this->recordEvent(new PluginDisabled(
            pluginId: $this->id->toString(),
            pluginName: $this->name->value,
            reason: $reason,
        ));
    }

    public function uninstall(): void
    {
        if ($this->status === PluginStatus::ENABLED) {
            throw new \DomainException('Plugin must be disabled before uninstalling');
        }

        $this->recordEvent(new PluginUninstalled(
            pluginId: $this->id->toString(),
            pluginName: $this->name->value,
            pluginClass: $this->class,
        ));
    }

    public function update(PluginVersion $newVersion): void
    {
        Assert::eq($this->version->value, $newVersion->value, 'PM0007::::plugin_is_already_at_this_version');
        Assert::eq($this->status, PluginStatus::ENABLED, 'PM0008::::plugin_update_is_blocked');

        $oldVersion = $this->version;
        $this->version = $newVersion;
        $this->lastAnalyzedAt = new \DateTimeImmutable();

        $this->recordEvent(new PluginUpdated(
            pluginId: $this->id->toString(),
            pluginName: $this->name->value,
            oldVersion: $oldVersion->value,
            newVersion: $newVersion->value,
        ));
    }

    public function blockUpdate(PluginVersion $attemptedVersion, string $reason): void
    {
        $this->status = PluginStatus::UPDATE_BLOCKED;
        $this->blockedVersion = $attemptedVersion;
        $this->blockedReason = $reason;
        $this->blockedAt = new \DateTimeImmutable();

        $this->recordEvent(new PluginUpdateBlocked(
            pluginId: $this->id->toString(),
            pluginName: $this->name->value,
            currentVersion: $this->version->value,
            blockedVersion: $attemptedVersion->value,
            reason: $reason,
        ));
    }

    public function markAsAnalyzed(): void
    {
        $this->lastAnalyzedAt = new \DateTimeImmutable();
    }

    public function updateMetadata(array $metadata): void
    {
        $this->metadata = Metadata::fromArray(array_merge($this->metadata->toArray(), $metadata));
    }

    public function isEnabled(): bool
    {
        return $this->status === PluginStatus::ENABLED;
    }

    public function isDisabled(): bool
    {
        return $this->status === PluginStatus::DISABLED;
    }

    public function isUpdateBlocked(): bool
    {
        return $this->status === PluginStatus::UPDATE_BLOCKED;
    }

    public function providesProtocol(): bool
    {
        return $this->capabilities['provides_protocol'] ?? false;
    }

    public function providesAutomationFunctions(): bool
    {
        return $this->capabilities['provides_automation_functions'] ?? false;
    }

    public function providesWebhooks(): bool
    {
        return $this->capabilities['provides_webhooks'] ?? false;
    }
}
