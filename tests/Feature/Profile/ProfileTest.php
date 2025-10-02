<?php

use App\Models\{User, Reservation, Book};
use Illuminate\Testing\Fluent\AssertableJson;

it('returns profile data with stats and recent activity', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create();
    Reservation::factory()->create([
        'user_id' => $user->id,
        'book_id' => $book->id,
        'status'  => 'active',
    ]);

    $this->actingAs($user);

    $response = $this->get('/profile');

    $response->assertStatus(200)
        ->assertJson(
            fn(AssertableJson $json) => $json
                ->has(
                    'user',
                    fn($u) => $u
                        ->where('id', $user->id)
                        ->hasAll(['name', 'email', 'role'])
                )
                ->has(
                    'stats',
                    fn($s) => $s
                        ->hasAll(['active_reservations', 'total_borrowed', 'account_status'])
                )
                ->has('recent_activity')
        );
});

it('requires authentication to view profile', function () {
    $response = $this->get('/profile');
    $response->assertStatus(401); // expecting unauthorized for unauthenticated access
});
