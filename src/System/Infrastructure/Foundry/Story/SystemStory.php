<?php

namespace App\System\Infrastructure\Foundry\Story;

use App\System\Infrastructure\Foundry\Factory\PluginStatusFactory;
use App\System\Infrastructure\Foundry\Factory\UserFactory;
use App\System\Infrastructure\Foundry\Factory\UserStatusFactory;
use App\System\Infrastructure\Foundry\Factory\UserTypeFactory;
use Zenstruck\Foundry\Story;

class SystemStory extends Story
{
    private array $statuses = [];
    private array $types = [];

    public function build(): void
    {
        $this->createUserType();
        $this->createUserStatus();
        $this->createPluginStatus();
        $this->createUser();
    }

    private function createUserType(): void
    {
        foreach (UserTypeFactory::getDatas() as $data) {
            $this->types[$data['reference']] = UserTypeFactory::createOne($data);
        }
    }

    private function createUserStatus(): void
    {
        foreach (UserStatusFactory::getDatas() as $data) {
            $this->statuses[$data['reference']] = UserStatusFactory::createOne($data);
        }
    }

    private function createPluginStatus(): void
    {
        foreach (PluginStatusFactory::getDatas() as $data) {
            PluginStatusFactory::createOne($data);
        }
    }

    private function createUser(): void
    {
        foreach (UserFactory::getDatas() as $data) {
            $data = array_merge($data, [
                'status' => $this->statuses[$data['status']],
                'type' => $this->types[$data['type']],
            ]);
            UserFactory::createOne($data);
        }
    }
}
