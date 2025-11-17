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

namespace Marvin\Device\Presentation\Api\Dto\Input;

use Marvin\Device\Domain\ValueObject\CompositeStrategy;
use Marvin\Device\Domain\ValueObject\ExecutionStrategy;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Uuid;

final class CreateSceneDto
{
    #[NotBlank]
    public string $label;

    public ?array $sceneStates = null;

    #[Choice(choices: [
        CompositeStrategy::NATIVE_IF_AVAILABLE->value,
        CompositeStrategy::NATIVE_ONLY->value,
        CompositeStrategy::EMULATED_ONLY->value,
    ])]
    public string $compositeStrategy = CompositeStrategy::NATIVE_IF_AVAILABLE->value;

    #[Choice(choices: [
        ExecutionStrategy::BROADCAST->value,
        ExecutionStrategy::SEQUENTIAL->value,
        ExecutionStrategy::FIRST_RESPONSE->value,
        ExecutionStrategy::AGGREGATE->value,
    ])]
    public string $executionStrategy = ExecutionStrategy::SEQUENTIAL->value;

    #[Uuid(versions: Uuid::V7_MONOTONIC)]
    public ?string $zoneId = null;

    public ?string $description = null;

    public ?array $metadata = null;
}
