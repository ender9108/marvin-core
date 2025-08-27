<?php

namespace App\Domotic\Domain\ReferenceList;

use EnderLab\ToolsBundle\Service\EnumToArrayTrait;

enum CapabilityStateNotifyType: string
{
    use EnumToArrayTrait;

    // Sonores
    case BEEP = 'beep';
    case BEEP_LONG = 'beep_long';
    case CHIME = 'chime';
    case SIREN = 'siren';
    case MUTE = 'mute';

    // Visuelles
    case BLINK = 'blink';
    case FLASH = 'flash';
    case STROBE = 'strobe';
    case PULSE = 'pulse';

    // Vibrations
    case VIBRATE_SHORT = 'vibrate_short';
    case VIBRATE_LONG = 'vibrate_long';
    case VIBRATE_PATTERN = 'vibrate_pattern';

    // Textuelles
    case TEXT = 'text';
    case SCROLL_TEXT = 'scroll_text';
    case ICON = 'icon';

    // Combinées
    case SOUND_AND_LIGHT = 'sound_and_light';
    case SOUND_AND_VIBRATION = 'sound_and_vibration';
    case ALL = 'all';
}
