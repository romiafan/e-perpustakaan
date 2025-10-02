<?php

use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;

it('updates profile name and email', function () {
    $user = User::factory()->create([
        'password' => bcrypt('secret123'),
    ]);

    $this->actingAs($user);

    $payload = [
        'name'  => 'Updated Name',
        'email' => 'updated@example.com',
        'current_password' => 'secret123',
    ];

    $response = $this->patch('/profile', $payload);

    $response->assertStatus(200)
        ->assertJson(
            fn(AssertableJson $json) => $json
                ->has(
                    'user',
                    fn($u) => $u
                        ->where('name', 'Updated Name')
                        ->where('email', 'updated@example.com')
                )
                ->has('message')
        );
});

it('fails updating profile with invalid data', function () {
    $user = User::factory()->create([
        'password' => bcrypt('secret123'),
    ]);

    $this->actingAs($user);

    $response = $this->patch('/profile', [
        'email' => 'not-an-email',
    ]);

    $response->assertStatus(422)
        ->assertJson(
            fn(AssertableJson $json) => $json
                ->has('errors.email')
        );
});
