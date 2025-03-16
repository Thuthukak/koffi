<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barber;
use App\Models\User;

class BarberController extends Controller
{
    public function index(Request $request)
    {
        $barbers = Barber::with(['user', 'bookings' => function($query) use ($request) {
            $query->whereDate('bookingSlot', $request->date)
                  ->whereIn('status', ['queued', 'in-progress']);
        }])->get();
    
        return response()->json($barbers->map(function($barber) {
            return [
                'id' => $barber->id,
                'name' => $barber->user->name,
                'bio' => $barber->bio,
                'available' => $barber->bookings->count() < 8 // Max 8 bookings/day
            ];
        }));
    }
}
