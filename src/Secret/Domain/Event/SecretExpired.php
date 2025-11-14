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

namespace Marvin\Secret\Domain\Event;

use DateTimeImmutable;
use EnderLab\DddCqrsBundle\Domain\Event\AbstractDomainEvent;

final readonly class SecretExpired extends AbstractDomainEvent
{
    public function __construct(
        public string $secretId,
        public string $key,
        public string $scope,
        public string $category,
        public DateTimeImmutable $expiredAt,
    ) {
        parent::__construct();
    }

    public function toArray(): array
    {
        return [
            'secret_id' => $this->secretId,
            'key' => $this->key,
            'scope' => $this->scope,
            'category' => $this->category,
            'expired_at' => $this->expiredAt->format('c'),
            'occurred_on' => $this->occurredOn->format('c'),
        ];
    }
}
