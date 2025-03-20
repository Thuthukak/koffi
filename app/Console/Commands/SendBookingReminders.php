<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use Illuminate\Support\Facades\Notification;
use App\Notifications\BookingReminderNotification;
use Carbon\Carbon;

class SendBookingReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder notifications to customers whose bookings are in 15 minutes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $schedules = Booking::where('status', 'queued')
                    ->where('notification_sent', false)
                    ->whereRaw("expected_start_time <= NOW() + INTERVAL 15 MINUTE")
                    ->get();

        foreach ($schedules as $booking) {
            Notification::send($booking->client, new BookingReminderNotification($booking));
            $booking->update(['notification_sent' => true]);
        }

        $this->info('Booking reminders sent successfully!');
    }
    
}
