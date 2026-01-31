<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Schema\BaseJsonSchema;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Register extends Controller
{

    public function __invoke(Request $request)
    {
        try {

            $data = Validator::make($request->all(), [
                'name' => 'required|string|max:120',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
            ])->validate();

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $tokens = AuthService::issueTokens($user);

            return response()->json(
                (new BaseJsonSchema(
                    true,
                    'User registered',
                    ["user" => $tokens['user'], 'access_token' => $tokens['access_token']]
                ))->toArray(),
                201
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
                    'Registration failed'
                ))->toArray(),
                500
            );
        }
    }
}
