<?php

it('rejects request without token', function () {

    $this->getJson('/api/todos')
        ->assertStatus(401)
        ->assertJson([
            'success' => false
        ]);
});
