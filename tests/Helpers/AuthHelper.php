<?php

use App\Models\User;
use App\Services\AuthService;

function apiLoginUser(): array
{
    $user = User::factory()->create();

    $tokens = AuthService::issueTokens($user);

    return [$user, $tokens];
}
