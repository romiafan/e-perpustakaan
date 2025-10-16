<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationExpiredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Reservation $reservation
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Book Reservation Expired')
            ->line('Your reservation for "' . $this->reservation->book->title . '" has expired.')
            ->line('The book was reserved on ' . $this->reservation->reserved_at->format('F j, Y') . ' and expired on ' . $this->reservation->expires_at->format('F j, Y') . '.')
            ->line('You can make a new reservation if the book is still available.')
            ->action('Browse Books', url('/catalog'))
            ->line('Thank you for using our library!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'reservation_id' => $this->reservation->id,
            'book_title' => $this->reservation->book->title,
            'book_id' => $this->reservation->book_id,
            'expired_at' => $this->reservation->expires_at,
        ];
    }
}
