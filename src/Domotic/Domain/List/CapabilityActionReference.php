<?php

namespace Marvin\Domotic\Domain\List;

enum CapabilityActionReference: string
{
    case TURN_ON = 'turn_on';
    case TURN_OFF = 'turn_off';
    case SET_LEVEL = 'set_level';
    case SET_COLOR = 'set_color';
    case SET_COLOR_TEMP = 'set_color_temp';
    case OPEN = 'open';
    case CLOSE = 'close';
    case STOP = 'stop';
    case SET_POSITION = 'set_position';
    case FAN_ON = 'fan_on';
    case FAN_OFF = 'fan_off';
    case SET_SPEED = 'set_speed';
    case SET_MODE = 'set_mode';
    case SET_TARGET = 'set_target_temperature';
    case LOCK = 'lock';
    case UNLOCK = 'unlock';
    case ACTIVATE_SCENE = 'activate_scene';
    case BUTTON_PRESS = 'button_press';
    case BUTTON_DOUBLE = 'button_double';
    case BUTTON_LONG = 'button_long';
    case BUTTON_RELEASE = 'button_release';
}
