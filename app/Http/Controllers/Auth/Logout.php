<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Schema\BaseJsonSchema;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class Logout extends Controller
{
    public function __invoke(Request $request)
    {
        try {

            $refresh = $request->cookie('refresh_token');

            if ($refresh) {
                AuthService::logout($refresh);
            }

            return response()->json(
                (new BaseJsonSchema(
                    message: 'Logged out'
                ))->toArray()
            )->cookie(Cookie::forget('refresh_token'));

        } catch (\Throwable $e) {

            logger()->error($e);

            return response()->json(
                (new BaseJsonSchema(
                    false,
                    message: 'Logout failed'
                ))->toArray(),
                500
            )->cookie(Cookie::forget('refresh_token'));
        }
    }
}
