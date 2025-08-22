<?php

namespace App\Domotic\Domain\ReferenceList;

class CapabilityActionReferenceList
{
    // Switchable
    public const string TURN_ON  = 'turn_on';
    public const string TURN_OFF = 'turn_off';

    // Dimmable
    public const string SET_LEVEL = 'set_level';

    // Colorable
    public const string SET_COLOR = 'set_color';
    public const string SET_COLOR_TEMP = 'set_color_temp';

    // Coverable
    public const string OPEN   = 'open';
    public const string CLOSE  = 'close';
    public const string STOP   = 'stop';
    public const string SET_POSITION = 'set_position';

    // Fan
    public const string FAN_ON  = 'fan_on';
    public const string FAN_OFF = 'fan_off';
    public const string SET_SPEED = 'set_speed';

    // Thermostat
    public const string SET_MODE   = 'set_mode';
    public const string SET_TARGET = 'set_target_temperature';

    // Lockable
    public const string LOCK   = 'lock';
    public const string UNLOCK = 'unlock';

    // Scene
    public const string ACTIVATE_SCENE = 'activate_scene';

    // Button
    public const string BUTTON_PRESS   = 'button_press';
    public const string BUTTON_DOUBLE  = 'button_double';
    public const string BUTTON_LONG    = 'button_long';
    public const string BUTTON_RELEASE = 'button_release';
}
