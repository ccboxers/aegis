<?php

namespace Layman\Aegis\Support;

use Exception;

class Aegis
{
    protected string $privateKey;
    protected string $publicKey;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $config           = config('aegis');
        $this->privateKey = file_get_contents($config['private_path']);
        $this->publicKey  = file_get_contents($config['public_path']);
        if (!$this->privateKey || !$this->publicKey) {
            throw new Exception('Failed to load RSA keys. Check the config paths.');
        }
    }

    /**
     * get public key
     * @return string
     */
    public function getPublicKey(): string
    {
        return $this->publicKey;
    }


    /**
     * decrypt
     * @param string $encrypted AES key for public key encryption
     * @param string $nonce Random nonce for AES encryption
     * @param string $data AES encrypted data
     * @return array
     * @throws \Exception
     */
    public function decrypt(string $encrypted, string $nonce, string $data): array
    {
        try {
            if (!openssl_private_decrypt(base64_decode($encrypted), $aesKey, $this->privateKey, OPENSSL_PKCS1_OAEP_PADDING)) {
                throw new Exception('RSA decryption failed: ' . openssl_error_string());
            }
            $data = openssl_decrypt(base64_decode($data), 'AES-256-CBC', $aesKey, OPENSSL_RAW_DATA, base64_decode($nonce));
            return json_decode($data, true);
        } catch (\Throwable $exception) {
            throw new \Exception("Decryption failed: {$exception->getMessage()}");
        }
    }


    /**
     * signature
     * @param array $data
     * @return string
     * @throws Exception
     */
    public function signature(array $data): string
    {
        $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        if (!openssl_sign($data, $signature, $this->privateKey, OPENSSL_ALGO_SHA256)) {
            throw new Exception('Signing failed: ' . openssl_error_string());
        }
        return base64_encode($signature);
    }


    /**
     * Client encryption
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function clientEncryptData(array $data): array
    {
        $aesKey = random_bytes(32);

        if (!openssl_public_encrypt($aesKey, $encrypted, $this->publicKey, OPENSSL_PKCS1_OAEP_PADDING)) {
            throw new Exception('RSA encryption failed: ' . openssl_error_string());
        }

        $plaintext = json_encode($data, JSON_UNESCAPED_UNICODE);
        $nonce     = random_bytes(16);
        $data      = openssl_encrypt($plaintext, 'AES-256-CBC', $aesKey, OPENSSL_RAW_DATA, $nonce);

        return [
            'encrypted' => base64_encode($encrypted),
            'nonce' => base64_encode($nonce),
            'data' => base64_encode($data),
        ];
    }


    /**
     * Verify signature
     * @param array $data
     * @param string $signature
     * @return bool
     */
    public function clientVerifySignature(array $data, string $signature): bool
    {
        $raw       = json_encode($data, JSON_UNESCAPED_UNICODE);
        $signature = base64_decode($signature);
        $result    = openssl_verify($raw, $signature, $this->publicKey, OPENSSL_ALGO_SHA256);
        return $result === 1;
    }
}
