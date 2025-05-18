<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Booking;
use App\Models\Service;
use App\Models\Barber;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['service', 'client'])
        ->whereIn('status', ['queued', 'in-progress'])
        //order by created-at in descending order
        ->orderByAsc('created_at')
        ->get();

    $currentTime = Carbon::now();
    $totalTime = 0;
    $timePassed = 0;

    // Find the in-progress booking
    $inProgressBooking = $bookings->firstWhere('status', 'in-progress');

    if ($inProgressBooking && $inProgressBooking->start_time) {
        $timePassed = $currentTime->diffInMinutes(Carbon::parse($inProgressBooking->start_time));
    }

    $remainingTimeForCurrent = 0;

    // Adjust queue time dynamically
    $bookings->transform(function ($booking) use (&$totalTime, $timePassed, &$remainingTimeForCurrent) {
        $serviceDuration = $booking->service->duration ?? 0;

        if ($booking->status === 'in-progress') {
            $remainingTimeForCurrent = max(0, $serviceDuration - $timePassed);
            $totalTime += $remainingTimeForCurrent;
        } else {
            $totalTime += $serviceDuration;
        }

        $booking->timeRemaining = max(0, $totalTime - $timePassed);
        $booking->client_name = $booking->client->name ?? 'Unknown';

        return $booking;
    });

    return response()->json($bookings);
    }


    public function create(Request $request) //need to implement payment gateway in this method
{
    Log::info('we in the create booking method', ['request' => $request->all()]);
    
    $request->validate([
        'name' => 'required|string|max:255',
        'phoneNumber' => 'required|string|max:20',
        'email' => 'required|email|unique:clients,email',
        'service_id' => 'required|exists:services,id',
        'barber_id' => 'required|exists:barbers,id',
    ]);

    // Create or find the client by email
    $client = Client::firstOrCreate(
        ['email' => $request->email],
        [
            'name' => $request->name,
            'phoneNumber' => $request->phoneNumber,
        ]
    );

    Log::info('Client created/found', ['client' => $client]);

    // Retrieve the selected service to get its duration
    $service = Service::findOrFail($request->service_id);

    // Generate a unique reference code
    $reference = '#' . now()->format('dm') . '-' . Str::random(4);

    // Find the last booking for this barber that is still in progress or queued
    $lastBooking = Booking::where('barber_id', $request->barber_id)
                  ->whereIn('status', ['queued', 'in-progress'])
                  ->orderBy('expected_start_time', 'desc')
                  ->first();

    // Calculate expected start time and time remaining
    $startTime = $lastBooking
        ? Carbon::parse($lastBooking->expected_start_time)->addMinutes($service->duration)
        : now();

    $timeRemaining = $lastBooking
        ? $lastBooking->time_remaining + $service->duration
        : 0;

    // Create the booking
    $booking = Booking::create([
        'client_id' => $client->id,
        'reference' => $reference,
        'service_id' => $request->service_id,
        'barber_id' => $request->barber_id,
        'expected_start_time' => $startTime,
        'time_remaining' => $timeRemaining,
        'status' => 'queued',
        'skipCount' => 0,
    ]);

    Log::info('Booking created', ['booking' => $booking]);

    return response()->json([
        'message' => 'Booking successfully created!',
        'booking' => $booking,
    ], 201);
}


    //Create bookings from admin side for walk-in customers no payment gateway integration

    public function createWalkins(Request $request) //need to implement payment gateway in this method
    {
        Log::info('we in the create booking method', ['request' => $request->all()]);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phoneNumber' => 'required|string|max:20',
            'email' => 'required|email|unique:clients,email',
            'service_id' => 'required|exists:services,id',
            'barber_id' => 'required|exists:barbers,id',
        ]);
    
        // Create or find the client by email
        $client = Client::firstOrCreate(
            ['email' => $request->email],
            [
                'name' => $request->name,
                'phoneNumber' => $request->phoneNumber,
            ]
        );
    
        Log::info('Client created/found', ['client' => $client]);
    
        // Retrieve the selected service to get its duration
        $service = Service::findOrFail($request->service_id);
    
        // Generate a unique reference code
        $reference = '#' . now()->format('dm') . '-' . Str::random(4);
    
        // Find the last booking for this barber that is still in progress or queued
        $lastBooking = Booking::where('barber_id', $request->barber_id)
                      ->whereIn('status', ['queued', 'in-progress'])
                      ->orderBy('expected_start_time', 'desc')
                      ->first();
    
        // Calculate expected start time and time remaining
        $startTime = $lastBooking
            ? Carbon::parse($lastBooking->expected_start_time)->addMinutes($service->duration)
            : now();
    
        $timeRemaining = $lastBooking
            ? $lastBooking->time_remaining + $service->duration
            : 0;
    
        // Create the booking
        $booking = Booking::create([
            'client_id' => $client->id,
            'reference' => $reference,
            'service_id' => $request->service_id,
            'barber_id' => $request->barber_id,
            'expected_start_time' => $startTime,
            'time_remaining' => $timeRemaining,
            'status' => 'queued',
            'skipCount' => 0,
        ]);
    
        Log::info('Booking created', ['booking' => $booking]);
    
        return response()->json([
            'message' => 'Booking successfully created!',
            'booking' => $booking,
        ], 201);
    }


    // skip customer in queue

    public function skipBooking($bookingId)
{
    Log::info('we in the skip booking method', ['bookingId' => $bookingId]);
    
    $skippedBooking = Booking::findOrFail($bookingId);

    // Check if the customer has reached the max skip limit
    if ($skippedBooking->skipCount >= 3) {
        $skippedBooking->update(['status' => 'no-show', 'deleted_at' => now()]);
    } else {
        $skippedBooking->increment('skipCount');
        $skippedBooking->update(['skipped_at' => now()]);

    // Find the next booking in queue
    $nextBooking = Booking::where('barber_id', $skippedBooking->barber_id)
        ->where('status', 'queued')
        ->where('expected_start_time', '>', $skippedBooking->expected_start_time)
        ->orderBy('expected_start_time', 'asc')
        ->first();

    if (!$nextBooking) {
        return response()->json(['message' => 'No one else is in the queue to swap places with.']);
    }

    // Swap places: Update start times and time_remaining
    $originalStartTime = $skippedBooking->expected_start_time;

    $skippedBooking->update([
        'expected_start_time' => $nextBooking->expected_start_time,
        'time_remaining' => $nextBooking->time_remaining,
        'skipCount' => $skippedBooking->skipCount + 1,
    ]);

    $nextBooking->update([
        'expected_start_time' => $originalStartTime,
        'time_remaining' => max($skippedBooking->time_remaining - $nextBooking->service->duration, 0),
    ]);

}

    return response()->json(['message' => 'Customer skipped. They have been moved one place down.']);
}


    public function schedule()
    {
        $schedules = Booking::where('status', 'queued')
                    ->where('notification_sent', false)
                    ->whereRaw("expected_start_time <= NOW() + INTERVAL 15 MINUTE")
                    ->get();

        foreach ($schedules as $booking) {
            Notification::send($booking->client, new BookingReminderNotification($booking));
            $booking->update(['notification_sent' => true]);
        }
    
    }   

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line("Hello {$notifiable->name}, your booking is in 15 minutes!")
            ->line('Please arrive on time.');
    }

    //live queue
    public function liveQueue()
{
    $queue = Booking::with('client')
        ->whereIn('status', ['queued', 'in-progress'])
        ->orderBy('expected_start_time', 'asc')
        ->get()
        ->map(function ($booking) {
            $expectedStart = Carbon::parse($booking->expected_start_time);
            $now = Carbon::now();
            $timeRemaining = $expectedStart->greaterThan($now) ? $now->diffInMinutes($expectedStart) : 0;

            return [
                'reference' => $booking->reference,
                'name' => $booking->client->name,
                'status' => $booking->status,
                'time_remaining' => $timeRemaining, // Now dynamically calculated
            ];
        });

    return response()->json($queue);
}


    //admin

    public function adminBookings()
    {
        return view ('admin.bookings');
    }

    public function adminBookingsData()
    {
        $bookings = Booking::with([
      //in ascending order by created_at
            'client',
            'barber.user',
            'service',
        ])->orderBy('created_at', 'desc')->get();
    
        // Transform data to include the barber's name
        $bookings->transform(function ($booking) {
            return [
                'id' => $booking->id,
                'reference' => $booking->reference,
                'client' => [
                    'id' => $booking->client->id,
                    'name' => $booking->client->name,
                ],
                'barber' => [
                    'id' => $booking->barber->id,
                    'name' => $booking->barber->user->name ?? 'N/A', // Get barber's name from users table
                ],
                'service' => [
                    'id' => $booking->service->id,
                    'name' => $booking->service->name,
                    'duration' => $booking->service->duration,
                    'price' => $booking->service->price,
                ],
                'date' => $booking->date,
                'status' => $booking->status,
            ];
        });
    
        return response()->json($bookings);
    }
    
}
