<?php

use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;

it('logs in a user and returns expected structure', function () {
    $user = User::factory()->create([
        'password' => bcrypt('secret123'),
    ]);

    $response = $this->post('/login', [
        'email'    => $user->email,
        'password' => 'secret123',
    ]);

    $response->assertStatus(200)
        ->assertJson(
            fn(AssertableJson $json) => $json
                ->has(
                    'user',
                    fn($userJson) => $userJson
                        ->where('id', $user->id)
                        ->where('email', $user->email)
                        ->has('name')
                        ->has('role')
                )
                ->where('redirect', fn($r) => is_string($r))
        );
});

it('fails validation with wrong credentials', function () {
    $response = $this->post('/login', [
        'email'    => 'missing@example.com',
        'password' => 'wrong',
    ]);

    $response->assertStatus(422)
        ->assertJson(
            fn(AssertableJson $json) => $json
                ->has('message')
                ->has('errors.email')
        );
});
