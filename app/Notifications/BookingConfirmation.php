<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Booking;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmation extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function __construct(public Booking $booking) {}

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Booking Confirmation - Reference: ' . $this->booking->reference)
            ->line('Thank you for booking with us!')
            ->line('Booking Details:')
            ->line('Service: ' . $this->booking->service->name)
            ->line('Barber: ' . $this->booking->barber->user->name)
            ->line('Date & Time: ' . $this->booking->bookingSlot->format('F j, Y H:i'))
            ->action('View Booking', url('/bookings/' . $this->booking->id));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
