<?php

namespace App\Domotic\Infrastructure\Foundry\Factory;

use App\Domotic\Domain\Model\CapabilityAction;
use App\Domotic\Domain\ReferenceList\CapabilityActionReferenceList;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

class CapabilityActionFactory extends PersistentProxyObjectFactory
{
    private static array $datas = [
        ['label' => 'domotic.capability_actions.turn_on', 'reference' => CapabilityActionReferenceList::TURN_ON],
        ['label' => 'domotic.capability_actions.turn_off', 'reference' => CapabilityActionReferenceList::TURN_OFF],
        ['label' => 'domotic.capability_actions.set_level', 'reference' => CapabilityActionReferenceList::SET_LEVEL],
        ['label' => 'domotic.capability_actions.set_color', 'reference' => CapabilityActionReferenceList::SET_COLOR],
        ['label' => 'domotic.capability_actions.set_color_temp', 'reference' => CapabilityActionReferenceList::SET_COLOR_TEMP],
        ['label' => 'domotic.capability_actions.open', 'reference' => CapabilityActionReferenceList::OPEN],
        ['label' => 'domotic.capability_actions.close', 'reference' => CapabilityActionReferenceList::CLOSE],
        ['label' => 'domotic.capability_actions.stop', 'reference' => CapabilityActionReferenceList::STOP],
        ['label' => 'domotic.capability_actions.set_position', 'reference' => CapabilityActionReferenceList::SET_POSITION],
        ['label' => 'domotic.capability_actions.fan_on', 'reference' => CapabilityActionReferenceList::FAN_ON],
        ['label' => 'domotic.capability_actions.fan_off', 'reference' => CapabilityActionReferenceList::FAN_OFF],
        ['label' => 'domotic.capability_actions.set_speed', 'reference' => CapabilityActionReferenceList::SET_SPEED],
        ['label' => 'domotic.capability_actions.set_mode', 'reference' => CapabilityActionReferenceList::SET_MODE],
        ['label' => 'domotic.capability_actions.set_target_temperature', 'reference' => CapabilityActionReferenceList::SET_TARGET],
        ['label' => 'domotic.capability_actions.lock', 'reference' => CapabilityActionReferenceList::LOCK],
        ['label' => 'domotic.capability_actions.unlock', 'reference' => CapabilityActionReferenceList::UNLOCK],
        ['label' => 'domotic.capability_actions.activate_scene', 'reference' => CapabilityActionReferenceList::ACTIVATE_SCENE],
        ['label' => 'domotic.capability_actions.button_press', 'reference' => CapabilityActionReferenceList::BUTTON_PRESS],
        ['label' => 'domotic.capability_actions.button_double', 'reference' => CapabilityActionReferenceList::BUTTON_DOUBLE],
        ['label' => 'domotic.capability_actions.button_long', 'reference' => CapabilityActionReferenceList::BUTTON_LONG],
        ['label' => 'domotic.capability_actions.button_release', 'reference' => CapabilityActionReferenceList::BUTTON_RELEASE]
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
