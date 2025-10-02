<?php

use Illuminate\Testing\Fluent\AssertableJson;

it('executes full user registration and login flow', function () {
    $registerPayload = [
        'name'                  => 'Flow User',
        'email'                 => 'flowuser@example.com',
        'password'              => 'secret123',
        'password_confirmation' => 'secret123',
    ];

    $this->post('/register', $registerPayload)->assertStatus(201);

    $this->post('/login', [
        'email'    => 'flowuser@example.com',
        'password' => 'secret123',
    ])->assertStatus(200)
        ->assertJson(fn(AssertableJson $json) => $json->has('user')->has('redirect'));
});
