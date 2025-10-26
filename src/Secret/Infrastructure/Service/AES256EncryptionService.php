<?php

namespace Marvin\Secret\Infrastructure\Service;

use Marvin\Secret\Domain\Service\EncryptionServiceInterface;
use SensitiveParameter;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class AES256EncryptionService implements EncryptionServiceInterface
{
    private const string CIPHER_METHOD = 'aes-256-gcm';

    public function __construct(
        #[SensitiveParameter]
        #[Autowire(env: 'SECRET_MASTER_KEY')]
        private string $masterKey,
    ) {
        if (strlen($this->masterKey) !== 32) {
            /** @todo */
            throw new \InvalidArgumentException('Master key must be 32 bytes (256 bits)');
        }
    }

    public function encrypt(string $plainText): string
    {
        // Générer un IV aléatoire pour chaque encryption
        $ivLength = openssl_cipher_iv_length(self::CIPHER_METHOD);
        if ($ivLength === false) {
            /** @todo */
            // throw Encryption::encryptionFailed();
        }

        $iv = openssl_random_pseudo_bytes($ivLength);

        $tag = '';
        $cipherText = openssl_encrypt(
            $plainText,
            self::CIPHER_METHOD,
            $this->masterKey,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        if ($cipherText === false) {
            /** @todo */
            // throw Encryption::encryptionFailed();
        }

        // Format: base64(iv + tag + ciphertext)
        return base64_encode($iv . $tag . $cipherText);
    }

    public function decrypt(string $encryptedData): string
    {
        $data = base64_decode($encryptedData, true);

        if ($data === false) {
            /** @todo */
            // throw Encryption::decryptionFailed();
        }

        $ivLength = openssl_cipher_iv_length(self::CIPHER_METHOD);
        if ($ivLength === false) {
            /** @todo */
            // throw Encryption::decryptionFailed();
        }

        $tagLength = 16; // GCM tag is always 16 bytes

        $iv = substr($data, 0, $ivLength);
        $tag = substr($data, $ivLength, $tagLength);
        $cipherText = substr($data, $ivLength + $tagLength);

        $plainText = openssl_decrypt(
            $cipherText,
            self::CIPHER_METHOD,
            $this->masterKey,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        if ($plainText === false) {
            /** @todo */
            //throw Encryption::decryptionFailed();
        }

        return $plainText;
    }
}
