<?php

it('can register user', function () {

    $response = $this->postJson('/api/auth/register', [
        'name' => 'Jefy',
        'email' => 'jefy@test.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'success' => true,
            'message' => 'User registered'
        ]);
});
