<?php

namespace Marvin\Tests\Factory\System;

use Marvin\System\Domain\Model\ActionRequest;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

final class ActionRequestFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return ActionRequest::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'correlationId' => self::faker()->uuid(),
            'entityType' => 'container',
            'entityId' => self::faker()->uuid(),
            'action' => 'start',
        ];
    }

    public function completed(): self
    {
        return $this->afterInstantiate(function (ActionRequest $actionRequest): void {
            $actionRequest->markAsCompleted(true, 'Action completed successfully');
        });
    }

    public function failed(): self
    {
        return $this->afterInstantiate(function (ActionRequest $actionRequest): void {
            $actionRequest->markAsCompleted(false, null, 'Something went wrong');
        });
    }

    public function timeout(): self
    {
        return $this->afterInstantiate(function (ActionRequest $actionRequest): void {
            $actionRequest->markAsTimeout();
        });
    }
}
