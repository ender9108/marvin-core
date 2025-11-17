<?php

/**
 * Marvin Core - DDD-based home automation system
 *
 * @package   Marvin\Core
 * @author    Alexandre Berthelot <alexandreberthelot9108@gmail.com>
 * @copyright 2024-present Alexandre Berthelot
 * @license   AGPL-3.0 License
 * @link      https://github.com/ender9108/marvin-core
 */
declare(strict_types=1);

namespace Marvin\Secret\Infrastructure\Framework\Symfony\Service\Acl;

use Marvin\Secret\Domain\Exception\SecretNotFound;
use Marvin\Secret\Domain\Model\Secret;
use Marvin\Secret\Domain\Repository\SecretRepositoryInterface;
use Marvin\Secret\Domain\Service\EncryptionServiceInterface;
use Marvin\Secret\Domain\ValueObject\SecretCategory;
use Marvin\Secret\Domain\ValueObject\SecretKey;
use Marvin\Shared\Application\Service\Acl\Dto\SecretDto;
use Marvin\Shared\Application\Service\Acl\SecretQueryServiceInterface;
use RuntimeException;

/**
 * ACL Service for querying Secret Context from Protocol Context
 *
 * Retrieves encrypted secrets from Secret Context and provides them
 * in decrypted form for Protocol implementations
 */
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
            throw new RuntimeException("Secret '{$key}' has expired");
        }

        return $secret->value->decrypt($this->encryption);
    }

    public function getSecretInfo(string $key): SecretDto
    {
        $secretKey = new SecretKey($key);

        $secret = $this->secretRepository->byKey($secretKey);
        if ($secret === null) {
            throw SecretNotFound::withKey($secretKey);
        }

        return new SecretDto(
            key: $secret->key->value,
            scope: $secret->scope->value,
            category: $secret->category->value,
            autoRotate: $secret->rotationPolicy->isAutoRotate(),
            rotationIntervalDays: $secret->rotationPolicy->getRotationIntervalDays(),
            lastRotatedAt: $secret->lastRotatedAt,
            expiresAt: $secret->expiresAt,
            createdAt: $secret->createdAt,
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
        /** @var Secret $secret */
        foreach ($secrets as $secret) {
            if (!$secret->isExpired()) {
                $values[$secret->key->value] = $secret->value->decrypt($this->encryption);
            }
        }

        return $values;
    }
}
