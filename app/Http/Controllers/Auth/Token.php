<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Schema\BaseJsonSchema;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class Token extends Controller
{
    public function __invoke(Request $request)
    {
        try {

            $refreshToken = $request->cookie('refresh_token');

            if (! $refreshToken) {
                throw new \Exception('Missing refresh token');
            }

            $tokens = AuthService::refresh($refreshToken);
            return response()
                ->json(
                    (new BaseJsonSchema(
                        message: 'Login success',
                        data: [
                            'access_token' => $tokens['access_token'],
                            'user' => $tokens['user']
                        ]
                    ))->toArray()
                )->cookie(AuthService::getRefreshTokenCookieData($tokens['refresh_token']));
        } catch (\Throwable $e) {

            logger()->error($e);

            return response()->json(
                (new BaseJsonSchema(
                    success: false,
                    message: $e->getMessage()
                ))->toArray(),
                401
            );
        }
    }
}
