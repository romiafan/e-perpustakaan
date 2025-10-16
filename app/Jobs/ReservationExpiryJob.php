<?php

namespace App\Jobs;

use App\Models\Reservation;
use App\Notifications\ReservationExpiredNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ReservationExpiryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $expiredReservations = Reservation::with(['book', 'user'])
            ->where('status', 'active')
            ->where('expires_at', '<', now())
            ->get();

        foreach ($expiredReservations as $reservation) {
            $reservation->update(['status' => 'expired']);

            // Send notification to user
            $reservation->user->notify(new ReservationExpiredNotification($reservation));
        }

        if ($expiredReservations->count() > 0) {
            Log::info("Expired {$expiredReservations->count()} reservations and sent notifications");
        }
    }
}
