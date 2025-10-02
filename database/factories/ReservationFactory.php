<?php

namespace Database\Factories;

use App\Models\Reservation;
use App\Models\User;
use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Reservation>
 */
class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition(): array
    {
        $reservedAt = Carbon::now();
        return [
            'user_id'     => User::factory(),
            'book_id'     => Book::factory(),
            'status'      => 'active',
            'reserved_at' => $reservedAt,
            'expires_at'  => (clone $reservedAt)->addDays(7),
            'collected_at' => null,
        ];
    }

    public function expired(): self
    {
        return $this->state(function () {
            return [
                'status'     => 'expired',
                'reserved_at' => Carbon::now()->subDays(8),
                'expires_at' => Carbon::now()->subDay(),
            ];
        });
    }

    public function collected(): self
    {
        return $this->state(function () {
            return [
                'status'      => 'collected',
                'collected_at' => Carbon::now(),
            ];
        });
    }
}
