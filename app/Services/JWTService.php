<?php

namespace App\Services;

use App\Exceptions\InvalidJWT;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use PDO;

class JWTService
{

    private static $alg = 'HS256';
    private static function getKey()
    {
        return env("JWT_KEY",'secrnsindiasifnisgnineifnsifninsdfisgnsiet-key-for-tessting-sasfinti4sni');
    }

    static function sign($payload = [])
    {
        return JWT::encode($payload, self::getKey(), self::$alg);
    }

    static function verify(?string $token = '')
    {
        try {
            return JWT::decode($token, new Key(self::getKey(), self::$alg));
        } catch (\Throwable $th) {
            throw new InvalidJWT;
        }
    }
}
