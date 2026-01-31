<?php

namespace App\Services;

use App\Exceptions\InvalidJWT;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Cookie;
use Carbon\Carbon;
use Exception;

class AuthService
{
    private static $accessTokenMaxAge = 60 * 15;
    private static $refreshTokenMaxAge = 60 * 24 * 7;

    public static function authenticate(string $email, string $password): array
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! Auth::attempt(['email' => $email, 'password' => $password])) {
            throw new Exception('Invalid credentials');
        }

        return self::issueTokens($user);
    }

    public static function issueTokens(User $user): array
    {
        $accessToken = JWTService::sign([
            'sub' => $user->id,
            'iat' => time(),
            'exp' => time() + self::$accessTokenMaxAge,
        ]);

        $refreshToken = JWTService::sign([
            'sub' => $user->id,
            'iat' => time(),
            'exp' => time() + self::$refreshTokenMaxAge,
        ]);

        $user->refresh_token = $refreshToken;
        $user->save();

        return [
            'access_token'  => $accessToken,
            'refresh_token' => $refreshToken,
            'user'          => $user,
        ];
    }

    public static function refresh(string $refreshToken): array
    {
        try {
            $payload = JWTService::verify($refreshToken);
        } catch (\Throwable $e) {
            throw new InvalidJWT('Invalid or expired refresh token');
        }

        $user = User::find($payload->sub ?? null);

        if (! $user || $user->refresh_token !== $refreshToken) {
            throw new InvalidJWT('Invalid or expired refresh token');
        }

        return self::issueTokens($user);
    }


    public static function logout(User $user): void
    {
        $user->refresh_token = null;
        $user->save();
    }


    public static function getRefreshTokenCookieData(string $token): Cookie
    {
        return new Cookie(
            'refresh_token',
            $token,
            time() + self::$refreshTokenMaxAge,
            '/',
            null,
            env("APP_ENV") == 'production',
            true,
            false,
            Cookie::SAMESITE_LAX
        );
    }
}
