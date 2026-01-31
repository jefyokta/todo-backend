<?php

use App\Http\Controllers\Auth\Login;
use App\Http\Controllers\Auth\Logout;
use App\Http\Controllers\Auth\Register;
use App\Http\Controllers\Auth\Token;
use App\Http\Controllers\Todos;
use App\Http\Middleware\Authenticated;
use App\Http\Middleware\TodoOwnershipMiddleware;
use App\Http\Middleware\Unauthenticated;
use Illuminate\Support\Facades\Route;


Route::middleware(Unauthenticated::class)->group(function () {
    Route::post("/auth/login", Login::class);
    Route::post("/auth/register", Register::class);
});
Route::post("/auth/token", Token::class);

Route::middleware(Authenticated::class)->group(function () {
    Route::delete("/auth/logout", Logout::class);
    Route::apiResource("todos", Todos::class)
        ->middlewareFor(
            ['show', 'update', 'destroy'],
            TodoOwnershipMiddleware::class
        );
});
