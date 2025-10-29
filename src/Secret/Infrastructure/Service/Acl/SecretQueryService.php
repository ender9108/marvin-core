<?php

namespace Marvin\Secret\Infrastructure\Service\Acl;

use Exception;
use Marvin\Secret\Domain\Exception\SecretNotFound;
use Marvin\Secret\Domain\Repository\SecretRepositoryInterface;
use Marvin\Secret\Domain\Service\EncryptionServiceInterface;
use Marvin\Secret\Domain\ValueObject\SecretCategory;
use Marvin\Secret\Domain\ValueObject\SecretKey;
use Marvin\Shared\Application\Acl\SecretInfo;
use Marvin\Shared\Application\Acl\SecretQueryServiceInterface;

final readonly class SecretQueryService implements SecretQueryServiceInterface
{
    public function __construct(
        private SecretRepositoryInterface $secretRepository,
        private EncryptionServiceInterface $encryption,
    ) {
    }

    public function getSecretValue(string $key): string
    {
        $secretKey = new SecretKey($key);

        $secret = $this->secretRepository->byKey($secretKey);
        if ($secret === null) {
            throw SecretNotFound::withKey($secretKey);
        }

        if ($secret->isExpired()) {
            throw new \RuntimeException("Secret '{$key}' has expired");
        }

        return $secret->value->decrypt($this->encryption);
    }

    public function getSecretInfo(string $key): SecretInfo
    {
        $secretKey = new SecretKey($key);

        $secret = $this->secretRepository->byKey($secretKey);
        if ($secret === null) {
            throw SecretNotFound::withKey($secretKey);
        }

        return new SecretInfo(
            key: $secret->key->value,
            scope: $secret->scope->value,
            category: $secret->category->value,
            autoRotate: $secret->rotationPolicy->isAutoRotate(),
            rotationIntervalDays: $secret->rotationPolicy->getRotationIntervalDays(),
            lastRotatedAt: $secret->lastRotatedAt,
            expiresAt: $secret->expiresAt,
        );
    }

    public function exists(string $key): bool
    {
        try {
            $secretKey = new SecretKey($key);
            return $this->secretRepository->exists($secretKey);
        } catch (Exception) {
            return false;
        }
    }

    public function getSecretValues(array $keys): array
    {
        $values = [];

        foreach ($keys as $key) {
            try {
                $values[$key] = $this->getSecretValue($key);
            } catch (SecretNotFound) {
                continue;
            }
        }

        return $values;
    }

    public function getSecretsByCategory(string $category): array
    {
        $categoryEnum = SecretCategory::from($category);
        $secrets = $this->secretRepository->byCategory($categoryEnum);

        $values = [];
        foreach ($secrets as $secret) {
            if (!$secret->isExpired()) {
                $values[$secret->getKey()->toString()] = $secret->getValue()->decrypt($this->encryption);
            }
        }

        return $values;
    }
}
