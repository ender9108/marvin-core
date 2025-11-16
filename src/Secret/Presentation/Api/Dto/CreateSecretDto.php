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

namespace Marvin\Secret\Presentation\Api\Dto;

use DateTimeInterface;
use Marvin\Secret\Domain\ValueObject\SecretCategory;
use Marvin\Secret\Domain\ValueObject\SecretScope;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Regex;

final class CreateSecretDto
{
    #[Regex('/^[a-zA-Z0-9_.:-]{3,128}$/')]
    public string $key;

    public string $plainTextValue;

    #[Choice([
        SecretScope::GLOBAL->value,
        SecretScope::USER->value,
        SecretScope::DEVICE->value,
        SecretScope::PROTOCOL->value
    ])]
    public string $scope;

    #[Choice([
        SecretCategory::NETWORK->value,
        SecretCategory::INFRASTRUCTURE->value,
        SecretCategory::API_KEY->value,
        SecretCategory::CERTIFICATE->value
    ])]
    public string $category;

    public bool $managed = false;

    public int $rotationIntervalDays = 0;

    public bool $autoRotate = false;

    public ?string $rotationCommand = null;

    public ?DateTimeInterface $expiresAt = null;

    public ?array $metadata = null;
}
