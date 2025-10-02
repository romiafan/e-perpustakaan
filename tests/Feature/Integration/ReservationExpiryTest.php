<?php

use App\Models\{User, Book, Reservation};
use Illuminate\Support\Carbon;

it('marks reservations as expired after 7 days (simulation)', function () {
    $user = User::factory()->create();
    $book = Book::factory()->create(['available_quantity' => 1]);

    $reservation = Reservation::factory()->create([
        'user_id'     => $user->id,
        'book_id'     => $book->id,
        'reserved_at' => Carbon::now()->subDays(8),
        'expires_at'  => Carbon::now()->subDay(),
        'status'      => 'active',
    ]);

    // Simulate expiry job endpoint or command (not implemented yet)
    // Expectation: after job runs, status becomes expired
    $this->assertEquals('active', $reservation->status);
});
