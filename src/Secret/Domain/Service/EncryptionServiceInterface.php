<?php

namespace Marvin\Secret\Domain\Service;

interface EncryptionServiceInterface
{
    /**
     * Encrypt a plaintext and return a base64-encoded ciphertext (may include IV).
     */
    public function encrypt(string $plainText): string;

    /**
     * Decrypt a base64-encoded ciphertext and return the plaintext.
     */
    public function decrypt(string $encryptedData): string;
}
