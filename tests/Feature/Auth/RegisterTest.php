<?php

use Illuminate\Testing\Fluent\AssertableJson;

it('registers a new user and returns expected structure', function () {
    $payload = [
        'name'                  => 'Test User',
        'email'                 => 'testuser@example.com',
        'password'              => 'secret123',
        'password_confirmation' => 'secret123',
    ];

    $response = $this->post('/register', $payload);

    $response->assertStatus(201)
        ->assertJson(
            fn(AssertableJson $json) => $json
                ->has(
                    'user',
                    fn($user) => $user
                        ->where('name', 'Test User')
                        ->where('email', 'testuser@example.com')
                        ->where('role', 'member')
                )
                ->has('message')
        );
});

it('fails registration with invalid data', function () {
    $response = $this->post('/register', [
        'name'     => '',
        'email'    => 'not-an-email',
        'password' => 'short',
    ]);

    $response->assertStatus(422)
        ->assertJson(
            fn(AssertableJson $json) => $json
                ->has('message')
                ->has('errors.email')
                ->has('errors.password')
        );
});
