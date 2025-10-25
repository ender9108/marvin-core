<?php

namespace Marvin\Secret\Application\Service;

interface PasswordGeneratorInterface
{
    public function generate(int $length = 32, array $options = []): string;

    public function generateAlphanumeric(int $length = 32): string;
}
