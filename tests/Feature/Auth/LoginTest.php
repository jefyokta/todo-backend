<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('can login', function () {

    User::factory()->create([
        'email' => 'login@test.com',
        'password' => Hash::make('password123')
    ]);

    $response = $this->postJson('/api/auth/login', [
        'email' => 'login@test.com',
        'password' => 'password123'
    ]);

    $response->assertOk();
});
