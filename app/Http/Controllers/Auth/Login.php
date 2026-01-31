<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Schema\BaseJsonSchema;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Login extends Controller
{
    public function __invoke(Request $request)
    {
        try {

            $data = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string',
            ])->validate();

            $tokens = AuthService::authenticate(
                $data['email'],
                $data['password']
            );

            return response()->json(
                (new BaseJsonSchema(
                    true,
                    'Login success',
                    ["user" => $tokens['user'], 'access_token' => $tokens['access_token']]
                ))->toArray()
            )->cookie(AuthService::getRefreshTokenCookieData($tokens['refresh_token']));
        } catch (ValidationException $e) {

            return response()->json(
                (new BaseJsonSchema(
                    false,
                    'Validation failed',
                    null,
                    $e->errors()
                ))->toArray(),
                422
            );
        } catch (\Throwable $e) {
            return response()->json(
                (new BaseJsonSchema(
                    false,
                    'Invalid credentials',
                ))->toArray(),
                401
            );
        }
    }
}
