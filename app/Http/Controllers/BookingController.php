<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Booking;
use App\Models\Barber;
use App\Models\Service;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Notifications\BookingConfirmation;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['service', 'client'])
            ->whereIn('status', ['queued', 'in-progress'])
            ->orderBy('created_at')
            ->get();

        $currentTime = Carbon::now();
        $totalTime = 0;
        $timePassed = 0;

        $inProgressBooking = $bookings->firstWhere('status', 'in-progress');
        if ($inProgressBooking && $inProgressBooking->start_time) {
            $timePassed = $currentTime->diffInMinutes(
                Carbon::parse($inProgressBooking->start_time)
            );
        }

        $remainingTimeForCurrent = 0;

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

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'phone_number' => 'required_without:email|string|max:20',
                'email' => 'required_without:phone_number|email|max:255',
                'service_id' => 'required|exists:services,id',
                'barber_id' => 'required|exists:barbers,id',
                'booking_slot' => 'required|date|after:now'
            ]);

            $client = Client::firstOrCreate(
                ['email' => $validated['email']],
                [
                    'name' => $validated['name'],
                    'phone_number' => $validated['phone_number'],
                ]
            );

            $booking = Booking::create([
                'client_id' => $client->id,
                'service_id' => $validated['service_id'],
                'barber_id' => $validated['barber_id'],
                'booking_slot' => $validated['booking_slot'],
                'reference' => 'BOOK-' . Str::upper(Str::random(6)),
                'status' => 'queued',
                'skip_count' => 0,
            ]);

            return response()->json([
                'success' => true,
                'booking' => $booking,
                'message' => 'Booking created successfully'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }
}