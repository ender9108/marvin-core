<?php

interface EncryptionServiceInterface
{
    public function encrypt(string $plainText): string;

    public function decrypt(string $encryptedData): string;
}
