<?php

namespace Marvin\Domotic\Infrastructure\Framework\Symfony\DataFixtures\Foundry\Factory;

use Marvin\Domotic\Domain\List\CapabilityActionReference;
use Marvin\Domotic\Domain\Model\CapabilityAction;
use Marvin\Shared\Domain\ValueObject\Label;
use Marvin\Shared\Domain\ValueObject\Reference;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class CapabilityActionFactory extends PersistentProxyObjectFactory
{
    private static array $datas = [
        ['label' => 'domotic.capability_actions.turn_on', 'reference' => CapabilityActionReference::TURN_ON->value],
        ['label' => 'domotic.capability_actions.turn_off', 'reference' => CapabilityActionReference::TURN_OFF->value],
        ['label' => 'domotic.capability_actions.set_level', 'reference' => CapabilityActionReference::SET_LEVEL->value],
        ['label' => 'domotic.capability_actions.set_color', 'reference' => CapabilityActionReference::SET_COLOR->value],
        ['label' => 'domotic.capability_actions.set_color_temp', 'reference' => CapabilityActionReference::SET_COLOR_TEMP->value],
        ['label' => 'domotic.capability_actions.open', 'reference' => CapabilityActionReference::OPEN->value],
        ['label' => 'domotic.capability_actions.close', 'reference' => CapabilityActionReference::CLOSE->value],
        ['label' => 'domotic.capability_actions.stop', 'reference' => CapabilityActionReference::STOP->value],
        ['label' => 'domotic.capability_actions.set_position', 'reference' => CapabilityActionReference::SET_POSITION->value],
        ['label' => 'domotic.capability_actions.fan_on', 'reference' => CapabilityActionReference::FAN_ON->value],
        ['label' => 'domotic.capability_actions.fan_off', 'reference' => CapabilityActionReference::FAN_OFF->value],
        ['label' => 'domotic.capability_actions.set_speed', 'reference' => CapabilityActionReference::SET_SPEED->value],
        ['label' => 'domotic.capability_actions.set_mode', 'reference' => CapabilityActionReference::SET_MODE->value],
        ['label' => 'domotic.capability_actions.set_target_temperature', 'reference' => CapabilityActionReference::SET_TARGET->value],
        ['label' => 'domotic.capability_actions.lock', 'reference' => CapabilityActionReference::LOCK->value],
        ['label' => 'domotic.capability_actions.unlock', 'reference' => CapabilityActionReference::UNLOCK->value],
        ['label' => 'domotic.capability_actions.activate_scene', 'reference' => CapabilityActionReference::ACTIVATE_SCENE->value],
        ['label' => 'domotic.capability_actions.button_press', 'reference' => CapabilityActionReference::BUTTON_PRESS->value],
        ['label' => 'domotic.capability_actions.button_double', 'reference' => CapabilityActionReference::BUTTON_DOUBLE->value],
        ['label' => 'domotic.capability_actions.button_long', 'reference' => CapabilityActionReference::BUTTON_LONG->value],
        ['label' => 'domotic.capability_actions.button_release', 'reference' => CapabilityActionReference::BUTTON_RELEASE->value],
    ];

    protected function defaults(): array|callable
    {
        return [];
    }

    public static function getDatas(): array
    {
        return self::$datas;
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this
            ->beforeInstantiate(function (array $parameters): array {
                $parameters['label'] = new Label($parameters['label']);
                $parameters['reference'] = new Reference($parameters['reference']);
                return $parameters;
            })
        ;
    }

    public static function class(): string
    {
        return CapabilityAction::class;
    }
}
