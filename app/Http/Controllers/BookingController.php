<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Booking;
use App\Models\Barber;
use Illuminate\Support\Str;
use App\Notifications\BookingConfirmation;

class BookingController extends Controller
{
    public function index()
    {
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
