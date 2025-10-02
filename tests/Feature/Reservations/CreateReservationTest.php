<?php

use App\Models\{User, Book, Reservation};
use Illuminate\Testing\Fluent\AssertableJson;

it('creates a reservation and returns expected structure', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create(['available_quantity' => 3]);

    $this->actingAs($user);

    $response = $this->post('/reservations', [
        'book_id' => $book->id,
    ]);

    $response->assertStatus(201)
        ->assertJson(
            fn(AssertableJson $json) => $json
                ->has(
                    'reservation',
                    fn($r) => $r
                        ->where('book.id', $book->id)
                        ->where('status', 'active')
                        ->has('reserved_at')
                        ->has('expires_at')
                )
                ->has('message')
        );
});

it('prevents reservation when user already has active one', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create(['available_quantity' => 2]);
    Reservation::factory()->create([
        'user_id' => $user->id,
        'book_id' => $book->id,
        'status'  => 'active',
    ]);

    $this->actingAs($user);

    $response = $this->post('/reservations', [
        'book_id' => $book->id,
    ]);

    $response->assertStatus(422)
        ->assertJson(
            fn(AssertableJson $json) => $json
                ->has('errors.user')
        );
});
