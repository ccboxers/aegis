<?php

namespace Layman\Aegis\Facades;

use http\Encoding\Stream;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string getPublicKey()
 * @method static array decrypt(string $encrypted, string $nonce, string $data)
 * @method static string signature(array $data)
 * @method static array clientEncryptData(array $data)
 * @method static bool clientVerifySignature(array $data, string $signature)
 *
 * @see \Layman\Aegis\Support\Aegis
 */
class Aegis extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'aegis';
    }
}
