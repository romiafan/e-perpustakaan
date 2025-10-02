<?php

namespace App\Services;

use App\Models\{Reservation, User, Book};
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ReservationService
{
    /**
     * Create a reservation enforcing business rules.
     *
     * @throws \RuntimeException
     */
    public function createReservation(User $user, int $bookId): Reservation
    {
        return DB::transaction(function () use ($user, $bookId) {
            if ($user->reservations()->where('status', 'active')->exists()) {
                throw new \RuntimeException('You already have an active reservation');
            }

            $book = Book::lockForUpdate()->findOrFail($bookId);
            if ($book->available_quantity <= 0) {
                throw new \RuntimeException('Book is not available');
            }

            $reservedAt = Carbon::now();
            $reservation = Reservation::create([
                'user_id'     => $user->id,
                'book_id'     => $book->id,
                'status'      => 'active',
                'reserved_at' => $reservedAt,
                'expires_at'  => (clone $reservedAt)->addDays(7),
            ]);

            // Decrement availability optimistically
            $book->decrement('available_quantity');

            return $reservation->fresh(['book']);
        });
    }

    /** List user reservations grouped active/history. */
    public function listUserReservations(User $user): array
    {
        $reservations = $user->reservations()->with('book')->get();
        return [
            'active'  => $reservations->where('status', 'active')->values(),
            'history' => $reservations->whereIn('status', ['collected', 'expired', 'cancelled'])->values(),
        ];
    }

    /** Update reservation status (cancel/collect). */
    public function updateReservationStatus(User $user, Reservation $reservation, string $status): Reservation
    {
        if ($reservation->user_id !== $user->id) {
            throw new \RuntimeException('Cannot modify this reservation');
        }

        if ($status === 'cancelled' && $reservation->status === 'active') {
            $reservation->cancel();
            // Increment availability
            $reservation->book()->increment('available_quantity');
        } elseif ($status === 'collected' && $reservation->canBeCollected()) {
            $reservation->markAsCollected();
        }

        return $reservation->refresh();
    }

    /** Expire overdue reservations; returns count. */
    public function expireOverdueReservations(): int
    {
        return Reservation::where('status', 'active')
            ->where('expires_at', '<', Carbon::now())
            ->tap(function ($query) {
                // Additional logging or metrics hook
            })
            ->update(['status' => 'expired']);
    }
}
