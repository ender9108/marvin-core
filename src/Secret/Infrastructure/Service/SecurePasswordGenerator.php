<?php
/**
 * Marvin Core - DDD-based home automation system
 *
 * @package   Marvin\Core
 * @author    Alexandre Berthelot <alexandreberthelot9108@gmail.com>
 * @copyright 2024-present Alexandre Berthelot
 * @license   AGPL-3.0 License
 * @link      https://github.com/ender9108/marvin-core
 */

declare(strict_types=1);

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
