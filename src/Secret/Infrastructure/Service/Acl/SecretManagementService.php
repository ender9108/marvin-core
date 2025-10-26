<?php

namespace Marvin\Secret\Infrastructure\Service\Acl;

use EnderLab\DddCqrsBundle\Application\Command\SyncCommandBusInterface;
use Marvin\Secret\Application\Command\DeleteSecret;
use Marvin\Secret\Application\Command\RotateSecret;
use Marvin\Secret\Application\Command\StoreSecret;
use Marvin\Secret\Application\Command\UpdateSecret;
use Marvin\Secret\Domain\Exception\SecretNotFound;
use Marvin\Secret\Domain\Repository\SecretRepositoryInterface;
use Marvin\Secret\Domain\ValueObject\RotationPolicy;
use Marvin\Secret\Domain\ValueObject\SecretCategory;
use Marvin\Secret\Domain\ValueObject\SecretKey;
use Marvin\Secret\Domain\ValueObject\SecretScope;
use Marvin\Shared\Application\Acl\SecretManagementServiceInterface;
use Marvin\Shared\Domain\ValueObject\Metadata;
use Psr\Log\LoggerInterface;

final readonly class SecretManagementService implements SecretManagementServiceInterface
{
    public function __construct(
        private SyncCommandBusInterface $syncCommandBus,
        private SecretRepositoryInterface $secretRepository,
        private LoggerInterface $logger,
    ) {}

    public function createSecret(
        string $key,
        string $value,
        string $category,
        string $scope = 'device',
        bool $managed = false,
        int $rotationIntervalDays = 0,
        array $metadata = [],
    ): void {
        $this->logger->info('Creating secret via ACL', [
            'key' => $key,
            'category' => $category,
            'scope' => $scope,
            'managed' => $managed,
        ]);

        $this->syncCommandBus->handle(
            new StoreSecret(
                key: new SecretKey($key),
                plainTextValue: $value,
                scope: SecretScope::from($scope),
                category: SecretCategory::from($category),
                managed: $managed,
                rotationIntervalDays: $rotationIntervalDays,
                autoRotate: $managed && $rotationIntervalDays > 0,
                metadata: new Metadata($metadata),
            )
        );
    }

    public function updateSecret(string $key, string $value): void
    {
        $this->logger->info('Updating secret via ACL', ['key' => $key]);

        $this->syncCommandBus->handle(
            new UpdateSecret(
                key: new SecretKey($key),
                newValue: $value,
            )
        );
    }

    public function ensureSecret(
        string $key,
        string $value,
        string $category,
        string $scope = 'device',
        bool $managed = false,
        int $rotationIntervalDays = 0,
        array $metadata = [],
    ): bool {
        if ($this->secretRepository->exists(new SecretKey($key))) {
            $this->logger->debug('Secret already exists, skipping creation', ['key' => $key]);
            return false;
        }

        $this->createSecret(
            key: $key,
            value: $value,
            category: $category,
            scope: $scope,
            managed: $managed,
            rotationIntervalDays: $rotationIntervalDays,
            metadata: $metadata,
        );

        return true;
    }

    public function rotateSecret(string $key, ?string $newValue = null): void
    {
        $secretKey = new SecretKey($key);
        $secret = $this->secretRepository->byKey($secretKey);

        if ($secret === null) {
            throw SecretNotFound::withKey($secretKey);
        }

        if ($secret->rotationPolicy->getManagement()->isExternal() && $newValue === null) {
            /*
             * @todo
            throw new \InvalidArgumentException(
                "Cannot auto-generate value for external secret '{$key}'. Please provide a new value."
            );
            */
        }

        $this->logger->info('Rotating secret via ACL', [
            'key' => $key,
            'managed' => $secret->rotationPolicy->getManagement()->value,
            'auto_generated' => $newValue === null,
        ]);

        $this->syncCommandBus->handle(
            new RotateSecret(
                key: $secretKey,
                newValue: $newValue,
            )
        );
    }

    public function deleteSecret(string $key): void
    {
        $this->logger->info('Deleting secret via ACL', ['key' => $key]);

        $this->syncCommandBus->handle(
            new DeleteSecret(key: new SecretKey($key))
        );
    }

    public function updateRotationPolicy(
        string $key,
        bool $managed,
        int $rotationIntervalDays,
    ): void {
        $secretKey = new SecretKey($key);
        $secret = $this->secretRepository->byKey($secretKey);

        if ($secret === null) {
            throw SecretNotFound::withKey($secretKey);
        }

        $this->logger->info('Updating rotation policy via ACL', [
            'key' => $key,
            'managed' => $managed,
            'interval_days' => $rotationIntervalDays,
        ]);

        $newPolicy = $managed
            ? ($rotationIntervalDays > 0
                ? RotationPolicy::managed($rotationIntervalDays)
                : RotationPolicy::managedNoRotation())
            : RotationPolicy::external($rotationIntervalDays);

        $secret->updateRotationPolicy($newPolicy);
        $this->secretRepository->save($secret);
    }
}
