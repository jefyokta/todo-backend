<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use App\Schema\BaseJsonSchema;
use App\Services\JWTService;
use Symfony\Component\HttpFoundation\Response;

class Authenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {

            $header = $request->header('Authorization', '');

            if (! str_starts_with($header, 'Bearer ')) {
                return response()->json(
                    (new BaseJsonSchema(
                        false,
                        message: 'Missing bearer token'
                    ))->toArray(),
                    401
                );
            }

            $token = substr($header, 7);

            $payload = JWTService::verify($token);

            $user = User::find($payload->sub ?? null);
            if (! $user) {
                return response()->json(
                    (new BaseJsonSchema(
                        false,
                        message: 'User not found'
                    ))->toArray(),
                    401
                );
            }
            /** @disregard */
            $request->user = $user;

            return $next($request);
        } catch (\Throwable $e) {

            return response()->json(
                (new BaseJsonSchema(
                    false,
                    message: 'Invalid token'
                ))->toArray(),
                401
            );
        }
    }
}
