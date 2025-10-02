<?php

use App\Models\{User, Book, Reservation};
use Illuminate\Testing\Fluent\AssertableJson;

it('lists user reservations grouped by active and history', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create();
    Reservation::factory()->create([
        'user_id' => $user->id,
        'book_id' => $book->id,
        'status'  => 'active',
    ]);
    Reservation::factory()->expired()->create([
        'user_id' => $user->id,
        'book_id' => $book->id,
    ]);

    $this->actingAs($user);

    $response = $this->get('/reservations');

    $response->assertStatus(200)
        ->assertJson(
            fn(AssertableJson $json) => $json
                ->has('active')
                ->has('history')
        );
});
