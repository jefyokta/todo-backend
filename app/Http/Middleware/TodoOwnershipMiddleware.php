<?php

namespace App\Http\Middleware;

use App\Models\Todo;
use App\Schema\BaseJsonSchema;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TodoOwnershipMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $todo = $request->route('todo');

        if (! $todo instanceof Todo) {
            return response()->json(
                (new BaseJsonSchema(
                    false,
                    message: 'Todo not found'
                ))->toArray(),
                404
            );
        }

        if ($todo->user_id !== $request->user->id) {
            return response()->json(
                (new BaseJsonSchema(
                    false,
                    message: 'Forbidden'
                ))->toArray(),
                403
            );
        }

        return $next($request);
    }
}
