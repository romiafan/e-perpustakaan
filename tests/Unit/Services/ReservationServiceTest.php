<?php

use App\Models\{Book, Reservation, User};
use App\Services\ReservationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->service = new ReservationService();
});

describe('ReservationService', function () {
    describe('createReservation', function () {
        test('it creates a reservation with 7-day expiry', function () {
            $user = User::factory()->create();
            $book = Book::factory()->create(['available_quantity' => 5]);

            Carbon::setTestNow('2025-10-18 10:00:00');

            $reservation = $this->service->createReservation($user, $book->id);

            expect($reservation)->toBeInstanceOf(Reservation::class)
                ->and($reservation->user_id)->toBe($user->id)
                ->and($reservation->book_id)->toBe($book->id)
                ->and($reservation->status)->toBe('active')
                ->and($reservation->reserved_at->toDateTimeString())->toBe('2025-10-18 10:00:00')
                ->and($reservation->expires_at->toDateTimeString())->toBe('2025-10-25 10:00:00');
        });

        test('it decrements book available_quantity', function () {
            $user = User::factory()->create();
            $book = Book::factory()->create(['available_quantity' => 5]);

            $this->service->createReservation($user, $book->id);

            expect($book->fresh()->available_quantity)->toBe(4);
        });

        test('it throws exception when user has active reservation', function () {
            $user = User::factory()->create();
            $book1 = Book::factory()->create(['available_quantity' => 5]);
            $book2 = Book::factory()->create(['available_quantity' => 5]);

            // Create first reservation
            $this->service->createReservation($user, $book1->id);

            // Try to create second reservation
            expect(fn() => $this->service->createReservation($user, $book2->id))
                ->toThrow(\RuntimeException::class, 'You already have an active reservation');
        });

        test('it allows reservation after previous one is cancelled', function () {
            $user = User::factory()->create();
            $book1 = Book::factory()->create(['available_quantity' => 5]);
            $book2 = Book::factory()->create(['available_quantity' => 5]);

            // Create and cancel first reservation
            $reservation1 = $this->service->createReservation($user, $book1->id);
            $reservation1->update(['status' => 'cancelled']);

            // Should allow second reservation
            $reservation2 = $this->service->createReservation($user, $book2->id);

            expect($reservation2->status)->toBe('active');
        });

        test('it throws exception when book is not available', function () {
            $user = User::factory()->create();
            $book = Book::factory()->create(['available_quantity' => 0]);

            expect(fn() => $this->service->createReservation($user, $book->id))
                ->toThrow(\RuntimeException::class, 'Book is not available');
        });

        test('it throws exception when book not found', function () {
            $user = User::factory()->create();

            expect(fn() => $this->service->createReservation($user, 99999))
                ->toThrow(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        });

        test('it handles concurrent reservations with locking', function () {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();
            $book = Book::factory()->create(['available_quantity' => 1]);

            // First reservation succeeds
            $reservation1 = $this->service->createReservation($user1, $book->id);
            expect($reservation1->status)->toBe('active');

            // Second reservation should fail
            expect(fn() => $this->service->createReservation($user2, $book->id))
                ->toThrow(\RuntimeException::class, 'Book is not available');
        });

        test('it loads book relationship on created reservation', function () {
            $user = User::factory()->create();
            $book = Book::factory()->create(['available_quantity' => 5]);

            $reservation = $this->service->createReservation($user, $book->id);

            expect($reservation->relationLoaded('book'))->toBeTrue()
                ->and($reservation->book->id)->toBe($book->id);
        });
    });

    describe('listUserReservations', function () {
        test('it groups reservations into active and history', function () {
            $user = User::factory()->create();
            $book1 = Book::factory()->create(['available_quantity' => 5]);
            $book2 = Book::factory()->create(['available_quantity' => 5]);

            Reservation::factory()->create(['user_id' => $user->id, 'book_id' => $book1->id, 'status' => 'active']);
            Reservation::factory()->create(['user_id' => $user->id, 'book_id' => $book2->id, 'status' => 'collected']);

            $result = $this->service->listUserReservations($user);

            expect($result)->toHaveKey('active')
                ->and($result)->toHaveKey('history')
                ->and($result['active'])->toHaveCount(1)
                ->and($result['history'])->toHaveCount(1);
        });

        test('it includes book relationship', function () {
            $user = User::factory()->create();
            $book = Book::factory()->create(['available_quantity' => 5]);

            Reservation::factory()->create(['user_id' => $user->id, 'book_id' => $book->id, 'status' => 'active']);

            $result = $this->service->listUserReservations($user);

            expect($result['active'][0]->relationLoaded('book'))->toBeTrue()
                ->and($result['active'][0]->book->id)->toBe($book->id);
        });

        test('it separates expired reservations into history', function () {
            $user = User::factory()->create();
            $book1 = Book::factory()->create(['available_quantity' => 5]);
            $book2 = Book::factory()->create(['available_quantity' => 5]);

            Reservation::factory()->create(['user_id' => $user->id, 'book_id' => $book1->id, 'status' => 'active']);
            Reservation::factory()->create(['user_id' => $user->id, 'book_id' => $book2->id, 'status' => 'expired']);

            $result = $this->service->listUserReservations($user);

            expect($result['active'])->toHaveCount(1)
                ->and($result['history'])->toHaveCount(1)
                ->and($result['history'][0]->status)->toBe('expired');
        });

        test('it separates cancelled reservations into history', function () {
            $user = User::factory()->create();
            $book1 = Book::factory()->create(['available_quantity' => 5]);
            $book2 = Book::factory()->create(['available_quantity' => 5]);

            Reservation::factory()->create(['user_id' => $user->id, 'book_id' => $book1->id, 'status' => 'active']);
            Reservation::factory()->create(['user_id' => $user->id, 'book_id' => $book2->id, 'status' => 'cancelled']);

            $result = $this->service->listUserReservations($user);

            expect($result['active'])->toHaveCount(1)
                ->and($result['history'])->toHaveCount(1)
                ->and($result['history'][0]->status)->toBe('cancelled');
        });

        test('it returns empty arrays when user has no reservations', function () {
            $user = User::factory()->create();

            $result = $this->service->listUserReservations($user);

            expect($result['active'])->toBeEmpty()
                ->and($result['history'])->toBeEmpty();
        });
    });

    describe('updateReservationStatus', function () {
        test('it cancels active reservation', function () {
            $user = User::factory()->create();
            $book = Book::factory()->create(['available_quantity' => 3]);
            $reservation = Reservation::factory()->create([
                'user_id' => $user->id,
                'book_id' => $book->id,
                'status' => 'active',
            ]);

            $updated = $this->service->updateReservationStatus($user, $reservation, 'cancelled');

            expect($updated->status)->toBe('cancelled');
        });

        test('it increments book quantity when cancelling', function () {
            $user = User::factory()->create();
            $book = Book::factory()->create(['available_quantity' => 3]);
            $reservation = Reservation::factory()->create([
                'user_id' => $user->id,
                'book_id' => $book->id,
                'status' => 'active',
            ]);

            $this->service->updateReservationStatus($user, $reservation, 'cancelled');

            expect($book->fresh()->available_quantity)->toBe(4);
        });

        test('it marks reservation as collected', function () {
            $user = User::factory()->create();
            $book = Book::factory()->create(['available_quantity' => 3]);
            $reservation = Reservation::factory()->create([
                'user_id' => $user->id,
                'book_id' => $book->id,
                'status' => 'active',
            ]);

            $updated = $this->service->updateReservationStatus($user, $reservation, 'collected');

            expect($updated->status)->toBe('collected')
                ->and($updated->collected_at)->not->toBeNull();
        });

        test('it throws exception when user does not own reservation', function () {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();
            $book = Book::factory()->create(['available_quantity' => 5]);
            $reservation = Reservation::factory()->create([
                'user_id' => $user1->id,
                'book_id' => $book->id,
                'status' => 'active',
            ]);

            expect(fn() => $this->service->updateReservationStatus($user2, $reservation, 'cancelled'))
                ->toThrow(\RuntimeException::class, 'Cannot modify this reservation');
        });

        test('it does not cancel already cancelled reservation', function () {
            $user = User::factory()->create();
            $book = Book::factory()->create(['available_quantity' => 3]);
            $reservation = Reservation::factory()->create([
                'user_id' => $user->id,
                'book_id' => $book->id,
                'status' => 'cancelled',
            ]);
            $initialQuantity = $book->available_quantity;

            $updated = $this->service->updateReservationStatus($user, $reservation, 'cancelled');

            // Status stays cancelled but quantity should not change
            expect($updated->status)->toBe('cancelled')
                ->and($book->fresh()->available_quantity)->toBe($initialQuantity);
        });

        test('it returns fresh reservation instance', function () {
            $user = User::factory()->create();
            $book = Book::factory()->create(['available_quantity' => 5]);
            $reservation = Reservation::factory()->create([
                'user_id' => $user->id,
                'book_id' => $book->id,
                'status' => 'active',
            ]);

            $updated = $this->service->updateReservationStatus($user, $reservation, 'cancelled');

            expect($updated)->toBeInstanceOf(Reservation::class)
                ->and($updated->id)->toBe($reservation->id)
                ->and($updated->wasChanged())->toBeFalse(); // Fresh instance
        });
    });

    describe('expireOverdueReservations', function () {
        test('it expires reservations past expiry date', function () {
            Carbon::setTestNow('2025-10-18 10:00:00');

            $book = Book::factory()->create(['available_quantity' => 5]);

            // Create reservation that expired yesterday
            Reservation::factory()->create([
                'status' => 'active',
                'book_id' => $book->id,
                'expires_at' => Carbon::now()->subDay(),
            ]);

            $count = $this->service->expireOverdueReservations();

            expect($count)->toBe(1);
            $this->assertDatabaseHas('reservations', [
                'book_id' => $book->id,
                'status' => 'expired',
            ]);
        });

        test('it does not expire future reservations', function () {
            Carbon::setTestNow('2025-10-18 10:00:00');

            $book = Book::factory()->create(['available_quantity' => 5]);

            // Create reservation that expires tomorrow
            Reservation::factory()->create([
                'status' => 'active',
                'book_id' => $book->id,
                'expires_at' => Carbon::now()->addDay(),
            ]);

            $count = $this->service->expireOverdueReservations();

            expect($count)->toBe(0);
            $this->assertDatabaseHas('reservations', [
                'book_id' => $book->id,
                'status' => 'active',
            ]);
        });

        test('it only expires active reservations', function () {
            Carbon::setTestNow('2025-10-18 10:00:00');

            $book = Book::factory()->create(['available_quantity' => 5]);

            // Create already collected reservation past expiry
            Reservation::factory()->create([
                'status' => 'collected',
                'book_id' => $book->id,
                'expires_at' => Carbon::now()->subDay(),
            ]);

            $count = $this->service->expireOverdueReservations();

            expect($count)->toBe(0);
            $this->assertDatabaseHas('reservations', [
                'book_id' => $book->id,
                'status' => 'collected',
            ]);
        });

        test('it returns count of expired reservations', function () {
            Carbon::setTestNow('2025-10-18 10:00:00');

            $book = Book::factory()->create(['available_quantity' => 5]);

            // Create 3 expired active reservations
            Reservation::factory()->count(3)->create([
                'status' => 'active',
                'book_id' => $book->id,
                'expires_at' => Carbon::now()->subDay(),
            ]);

            $count = $this->service->expireOverdueReservations();

            expect($count)->toBe(3);
        });

        test('it returns zero when no reservations to expire', function () {
            $count = $this->service->expireOverdueReservations();

            expect($count)->toBe(0);
        });
    });
});
