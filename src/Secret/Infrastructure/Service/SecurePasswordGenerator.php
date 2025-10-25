<?php

namespace Marvin\Secret\Infrastructure\Service;

use Marvin\Secret\Application\Service\PasswordGeneratorInterface;
use Random\RandomException;

final class SecurePasswordGenerator implements PasswordGeneratorInterface
{
    /**
     * @throws RandomException
     */
    public function generate(int $length = 32, array $options = []): string
    {
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        $special = '!@#$%^&*()-_=+[]{}|;:,.<>?';

        $chars = $lowercase . $uppercase . $numbers;
        if ($options['include_special'] ?? true) {
            $chars .= $special;
        }

        $password = '';
        $maxIndex = strlen($chars) - 1;

        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, $maxIndex)];
        }

        return $password;
    }

    /**
     * @throws RandomException
     */
    public function generateAlphanumeric(int $length = 32): string
    {
        return bin2hex(random_bytes($length / 2));
    }
}
