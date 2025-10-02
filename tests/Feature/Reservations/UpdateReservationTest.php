<?php

use App\Models\{User, Book, Reservation};
use Illuminate\Testing\Fluent\AssertableJson;

it('cancels a reservation via PATCH', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create();
    $reservation = Reservation::factory()->create([
        'user_id' => $user->id,
        'book_id' => $book->id,
        'status'  => 'active',
    ]);

    $this->actingAs($user);

    $response = $this->patch("/reservations/{$reservation->id}", [
        'status' => 'cancelled',
    ]);

    $response->assertStatus(200)
        ->assertJson(
            fn(AssertableJson $json) => $json
                ->has(
                    'reservation',
                    fn($r) => $r
                        ->where('id', $reservation->id)
                        ->where('status', 'cancelled')
                )
                ->has('message')
        );
});

it('returns forbidden when modifying another user reservation', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $book = Book::factory()->create();
    $reservation = Reservation::factory()->create([
        'user_id' => $other->id,
        'book_id' => $book->id,
        'status'  => 'active',
    ]);

    $this->actingAs($user);

    $response = $this->patch("/reservations/{$reservation->id}", [
        'status' => 'cancelled',
    ]);

    $response->assertStatus(403)
        ->assertJson(
            fn(AssertableJson $json) => $json
                ->has('message')
        );
});
