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

namespace Marvin\Shared\Infrastructure\Cache;

use Marvin\Shared\Application\Service\Acl\Dto\SecretDto;
use Marvin\Shared\Application\Service\Acl\SecretQueryServiceInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;

final readonly class CacheableSecretQueryService implements SecretQueryServiceInterface
{
    private const int CACHE_TTL = 3600; // 1 heure
    private const string CACHE_PREFIX = 'secret.';

    public function __construct(
        private SecretQueryServiceInterface $decorated,
        private CacheItemPoolInterface $cache,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getSecretValue(string $key): string
    {
        $cacheKey = self::CACHE_PREFIX . $key;
        $item = $this->cache->getItem($cacheKey);

        if ($item->isHit()) {
            $this->logger->debug('Secret cache hit', ['key' => $key]);
            return $item->get();
        }

        $this->logger->debug('Secret cache miss', ['key' => $key]);

        $value = $this->decorated->getSecretValue($key);

        $item->set($value);
        $item->expiresAfter(self::CACHE_TTL);
        $this->cache->save($item);

        return $value;
    }

    public function getSecretInfo(string $key): SecretDto
    {
        return $this->decorated->getSecretInfo($key);
    }

    public function exists(string $key): bool
    {
        return $this->decorated->exists($key);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getSecretValues(array $keys): array
    {
        $result = [];
        $missingKeys = [];

        foreach ($keys as $key) {
            $cacheKey = self::CACHE_PREFIX . $key;
            $item = $this->cache->getItem($cacheKey);

            if ($item->isHit()) {
                $result[$key] = $item->get();
            } else {
                $missingKeys[] = $key;
            }
        }

        if (!empty($missingKeys)) {
            $freshValues = $this->decorated->getSecretValues($missingKeys);

            foreach ($freshValues as $key => $value) {
                $cacheKey = self::CACHE_PREFIX . $key;
                $item = $this->cache->getItem($cacheKey);
                $item->set($value);
                $item->expiresAfter(self::CACHE_TTL);
                $this->cache->save($item);

                $result[$key] = $value;
            }
        }

        return $result;
    }

    public function getSecretsByCategory(string $category): array
    {
        return $this->decorated->getSecretsByCategory($category);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function invalidate(string $key): void
    {
        $cacheKey = self::CACHE_PREFIX . $key;
        $this->cache->deleteItem($cacheKey);

        $this->logger->debug('Secret cache invalidated', ['key' => $key]);
    }
}
