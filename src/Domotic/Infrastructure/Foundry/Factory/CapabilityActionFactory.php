<?php

namespace App\Domotic\Infrastructure\Foundry\Factory;

use App\Domotic\Domain\Model\CapabilityAction;
use App\Domotic\Domain\ReferenceList\CapabilityActionReferenceList;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class CapabilityActionFactory extends PersistentProxyObjectFactory
{
    private static array $datas = [
        ['label' => 'domotic.capability_actions.turn_on', 'reference' => CapabilityActionReferenceList::TURN_ON->value],
        ['label' => 'domotic.capability_actions.turn_off', 'reference' => CapabilityActionReferenceList::TURN_OFF->value],
        ['label' => 'domotic.capability_actions.set_level', 'reference' => CapabilityActionReferenceList::SET_LEVEL->value],
        ['label' => 'domotic.capability_actions.set_color', 'reference' => CapabilityActionReferenceList::SET_COLOR->value],
        ['label' => 'domotic.capability_actions.set_color_temp', 'reference' => CapabilityActionReferenceList::SET_COLOR_TEMP->value],
        ['label' => 'domotic.capability_actions.open', 'reference' => CapabilityActionReferenceList::OPEN->value],
        ['label' => 'domotic.capability_actions.close', 'reference' => CapabilityActionReferenceList::CLOSE->value],
        ['label' => 'domotic.capability_actions.stop', 'reference' => CapabilityActionReferenceList::STOP->value],
        ['label' => 'domotic.capability_actions.set_position', 'reference' => CapabilityActionReferenceList::SET_POSITION->value],
        ['label' => 'domotic.capability_actions.fan_on', 'reference' => CapabilityActionReferenceList::FAN_ON->value],
        ['label' => 'domotic.capability_actions.fan_off', 'reference' => CapabilityActionReferenceList::FAN_OFF->value],
        ['label' => 'domotic.capability_actions.set_speed', 'reference' => CapabilityActionReferenceList::SET_SPEED->value],
        ['label' => 'domotic.capability_actions.set_mode', 'reference' => CapabilityActionReferenceList::SET_MODE->value],
        ['label' => 'domotic.capability_actions.set_target_temperature', 'reference' => CapabilityActionReferenceList::SET_TARGET->value],
        ['label' => 'domotic.capability_actions.lock', 'reference' => CapabilityActionReferenceList::LOCK->value],
        ['label' => 'domotic.capability_actions.unlock', 'reference' => CapabilityActionReferenceList::UNLOCK->value],
        ['label' => 'domotic.capability_actions.activate_scene', 'reference' => CapabilityActionReferenceList::ACTIVATE_SCENE->value],
        ['label' => 'domotic.capability_actions.button_press', 'reference' => CapabilityActionReferenceList::BUTTON_PRESS->value],
        ['label' => 'domotic.capability_actions.button_double', 'reference' => CapabilityActionReferenceList::BUTTON_DOUBLE->value],
        ['label' => 'domotic.capability_actions.button_long', 'reference' => CapabilityActionReferenceList::BUTTON_LONG->value],
        ['label' => 'domotic.capability_actions.button_release', 'reference' => CapabilityActionReferenceList::BUTTON_RELEASE->value],
    ];

    protected function defaults(): array|callable
    {
        return [];
    }

    public static function getDatas(): array
    {
        return self::$datas;
    }

    public static function class(): string
    {
        return CapabilityAction::class;
    }
}
