<?php

use App\Models\Todo;

it('can get todos with valid token', function () {

    [$user, $tokens] = apiLoginUser();

    Todo::factory()->count(3)->create([
        'user_id' => $user->id
    ]);

    $this->withHeader('Authorization', 'Bearer ' . $tokens['access_token'])
        ->getJson('/api/todos')
        ->assertOk()
        ->assertJson([
            'success' => true
        ])
        ->assertJsonStructure([
            'data' => [
                'data',
                'current_page'
            ]
        ]);
});

