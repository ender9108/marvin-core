<?php

namespace Marvin\Security\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Story;

use Marvin\Security\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Factory\DockerFactory;
use Marvin\Security\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Factory\UserStatusFactory;
use Marvin\Security\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Factory\UserTypeFactory;
use Zenstruck\Foundry\Story;

class SecurityStory extends Story
{
    private array $statuses = [];
    private array $types = [];

    public function build(): void
    {
        $this->createUserType();
        $this->createUserStatus();
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

    private function createUser(): void
    {
        foreach (DockerFactory::getDatas() as $data) {
            $data = array_merge($data, [
                'status' => $this->statuses[$data['status']],
                'type' => $this->types[$data['type']],
            ]);
            DockerFactory::createOne($data);
        }
    }
}
