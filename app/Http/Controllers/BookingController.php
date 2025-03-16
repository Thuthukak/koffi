<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Booking;
use App\Models\Service;
use App\Models\Barber;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Barber;
use Illuminate\Support\Str;
use App\Notifications\BookingConfirmation;
use App\Models\Barber;
use Illuminate\Support\Str;
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


    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phoneNumber' => 'required|string|max:20',
            'email' => 'required|email|unique:clients,email',
            'service_id' => 'required|exists:services,id',
            'barber_id' => 'required|exists:barbers,id',
        ]);

        // Create the client (or find existing one by email)
        $client = Client::firstOrCreate(
            ['email' => $request->email],
            [
                'name' => $request->name,
                'phoneNumber' => $request->phoneNumber,
            ]
        );

        // Generate a unique reference code
        $reference = '#' . now()->format('dm') . '-' . Str::random(4);

        // Create the booking
        $booking = Booking::create([
            'client_id' => $client->id,
            'reference' => $reference,
            'service_id' => $request->service_id,
            'barber_id' => $request->barber_id,
            'status' => 'queued',
            'skipCount' => 0,
        ]);

        return response()->json([
            'message' => 'Booking successfully created!',
            'booking' => $booking,
        ], 201);
        return Barber::with('user')->get()->map(function($barber) {
            return [
                'id' => $barber->id,
                'name' => $barber->user->name,
                'specialty' => $barber->specialty,
                'experience' => $barber->experience,
                'rating' => $barber->rating
            ];
        });
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'phone_number' => 'required_without:email|string|max:20',
                'email' => 'required_without:phone_number|email|max:255',
                'service' => 'required|exists:services,id',
                'barber' => 'required|exists:barbers,id',
                'bookingSlot' => 'required|date|after:now'
            ]);

        return Barber::with('user')->get()->map(function($barber) {
            return [
                'id' => $barber->id,
                'name' => $barber->user->name,
                'specialty' => $barber->specialty,
                'experience' => $barber->experience,
                'rating' => $barber->rating
            ];
        });
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'phone_number' => 'required_without:email|string|max:20',
                'email' => 'required_without:phone_number|email|max:255',
                'service' => 'required|exists:services,id',
                'barber' => 'required|exists:barbers,id',
                'bookingSlot' => 'required|date|after:now'
            ]);

            $client = Client::firstOrCreate(
                ['email' => $request->email],
                [
                    'name' => $request->name,
                    'phoneNumber' => $request->phone_number,
                    'email' => $request->email
                ]
            );

            $booking = Booking::create([
                'client_id' => $client->id,
                'service_id' => $request->service,
                'barber_id' => $request->barber,
                'bookingSlot' => $request->bookingSlot,
                'reference' => Str::uuid(),
                'status' => 'queued'
            ]);

            return response()->json([
                'success' => true,
                'booking' => $booking,
                'message' => 'Booking created successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }
}
