<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Barber;
use App\Models\Booking;
use App\Models\Service;
use App\Events\QueueUpdated;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class QueueController extends Controller
{
    public function getCurrentQueue()
    {
        $bookings = Booking::with(['service', 'client'])
            ->whereIn('status', ['queued', 'in-progress'])
            ->orderBy('created_at')
            ->get();

        $currentTime = Carbon::now();
        $totalWaitTime = 0;
        
        // Find the in-progress booking
        $inProgressBooking = $bookings->firstWhere('status', 'in-progress');
        $queueActive = $inProgressBooking !== null;
        
        // Process the queue with timing info
        $processedBookings = $bookings->map(function ($booking, $index) use ($currentTime, &$totalWaitTime, $inProgressBooking) {
            $serviceDuration = $booking->service->duration ?? 30;
            
            // Calculate remaining time for in-progress booking
            if ($booking->status === 'in-progress' && $booking->start_time) {
                $elapsedMinutes = $currentTime->diffInMinutes(Carbon::parse($booking->start_time));
                $remainingMinutes = max(0, $serviceDuration - $elapsedMinutes);
                
                if ($elapsedMinutes > $serviceDuration) {
                    // Booking is in overtime
                    $booking->overtime = $elapsedMinutes - $serviceDuration;
                    $booking->remainingTime = 0;
                } else {
                    $booking->overtime = 0;
                    $booking->remainingTime = $remainingMinutes;
                }
                
                $totalWaitTime = $remainingMinutes;
            } else {
                // For queued bookings, add to the total wait time
                $booking->position = $index;
                $booking->waitTime = $totalWaitTime;
                $booking->remainingTime = $totalWaitTime;
                $totalWaitTime += $serviceDuration;
            }
            
            return $booking;
        });
        
        return response()->json([
            'current' => $inProgressBooking,
            'waiting' => $processedBookings->where('status', 'queued')->values(),
            'queueActive' => $queueActive
        ]);
    }
    
    public function startQueue()
    {
        // Find the first booking in the queue
        $firstBooking = Booking::where('status', 'queued')
            ->orderBy('created_at')
            ->first();
        
        if (!$firstBooking) {
            return response()->json(['message' => 'No clients in queue'], 404);
        }
        
        // Set it to in-progress
        $firstBooking->update([
            'status' => 'in-progress',
            'start_time' => now()
        ]);
        
        // Notify next client if they exist
        $this->notifyNextClient();
        
        // Broadcast queue update
        broadcast(new QueueUpdated())->toOthers();
        
        return response()->json(['message' => 'Queue started', 'current' => $firstBooking]);
    }
    
    public function nextClient()
    {
        // Find the current in-progress booking
        $currentBooking = Booking::where('status', 'in-progress')->first();
        
        if ($currentBooking) {
            // Mark the current booking as completed
            $startTime = Carbon::parse($currentBooking->start_time);
            $endTime = now();
            $actualDuration = $startTime->diffInMinutes($endTime);
            
            $currentBooking->update([
                'status' => 'completed',
                'end_time' => $endTime,
                'actual_duration' => $actualDuration
            ]);
        }
        
        // Find the next client in queue
        $nextBooking = Booking::where('status', 'queued')
            ->orderBy('created_at')
            ->first();
        
        if (!$nextBooking) {
            return response()->json(['message' => 'No more clients in queue'], 200);
        }
        
        // Set the next booking to in-progress
        $nextBooking->update([
            'status' => 'in-progress',
            'start_time' => now()
        ]);
        
        // Notify the next client
        $this->notifyNextClient();
        
        // Broadcast queue update
        broadcast(new QueueUpdated())->toOthers();
        
        return response()->json([
            'message' => 'Moved to next client',
            'previous' => $currentBooking,
            'current' => $nextBooking
        ]);
    }
    
    private function notifyNextClient()
    {
        // Find the next client in queue
        $nextInLine = Booking::where('status', 'queued')
            ->orderBy('created_at')
            ->first();
            
        // Find the client after the next one
        $secondInLine = Booking::where('status', 'queued')
            ->orderBy('created_at')
            ->skip(1)
            ->first();
        
        if ($secondInLine && !$secondInLine->notified) {
            // Send notification to the client who is second in line
            $client = $secondInLine->client;
            
            // Send notification via email
            if ($client && $client->email) {
                // Mail::to($client->email)->send(new AlmostYourTurn($secondInLine));
                
                // Mark as notified
                $secondInLine->update(['notified' => true]);
            }
        }
    }

    

    /**
     * Remove/Cancel a client from the queue (soft delete)
     */
    public function removeClient($id)
    {
        // Find the booking to remove
        $booking = Booking::find($id);
        
        if (!$booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }
        
        // Only allow removal of queued bookings (not in-progress or completed)
        if ($booking->status !== 'queued') {
            return response()->json(['message' => 'Can only remove clients that are waiting in queue'], 400);
        }
        
        // Soft delete the booking
        // $booking->delete();
        
        // Update the booking status
        $booking->update(['status' => 'skipped']);
        
        
        // Log the removal for audit purposes
        Log::info('Booking removed from queue', [
            'booking_id' => $booking->id,
            'client_name' => $booking->client->name,
            'reference' => $booking->reference,
            'removed_at' => now()
        ]);
        
        // Broadcast queue update to refresh all connected clients
        broadcast(new QueueUpdated())->toOthers();
        
        return response()->json([
            'message' => 'Client removed from queue',
            'booking_id' => $booking->id
        ]);
    }
    
    public function addWalkinClient(Request $request)
    {
        Log::info($request->all());
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phoneNumber' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'service_id' => 'required|exists:services,id',
            'barber_id' => 'required|exists:barbers,id',
        ]);
        
        
        // Find or create client
        $client = Client::firstOrCreate(
            ['email' => $request->email ?: $request->phoneNumber],
            [
                'name' => $request->name,
                'phoneNumber' => $request->phoneNumber,
                'email' => $request->email,
            ]
            
        );
        Log::info('Client created/found', ['client' => $client]);
        
        // Create a new booking
        $booking = Booking::create([
            'client_id' => $client->id,
            'service_id' => $request->service_id,
            'barber_id' => $request->barber_id,
            'reference' => 'WLK-' . strtoupper(Str::random(6)),
            'status' => 'queued',
            'skipCount' => 0,
            'bookingSlot' => now()->format('Y-m-d H:i:s'), // Use current time
            'notified' => false
        ]);
        
        Log::info('Booking created', ['booking' => $booking]);
        
        // Broadcast queue update
        broadcast(new QueueUpdated())->toOthers();
        
        return response()->json([
            'message' => 'Walk-in client added to queue',
            'booking' => $booking->load(['client', 'service', 'barber'])
        ]);
    }
    
    public function getCompletedBookingsToday()
    {
        $today = Carbon::today();
        
        $completedBookings = Booking::with(['client', 'service'])
            ->where('status', 'completed')
            ->whereDate('end_time', $today)
            ->get();
            
        return response()->json($completedBookings);
    }
    
    public function reopenBooking($id)
    {
        // Find the booking
        $booking = Booking::find($id);
        
        if (!$booking) {
            return response()->json(['message' => 'Booking not found'], 404);
        }
        
        // Only reopen if it was completed, no-show, or cancelled
        if (!in_array($booking->status, ['completed', 'no-show', 'cancelled'])) {
            return response()->json(['message' => 'Booking cannot be reopened'], 400);
        }
        
        // Reopen the booking
        $booking->update([
            'status' => 'queued',
            'start_time' => null,
            'end_time' => null,
            'actual_duration' => null
        ]);
        
        // Broadcast queue update
        broadcast(new QueueUpdated())->toOthers();
        
        return response()->json([
            'message' => 'Booking reopened and added to queue',
            'booking' => $booking
        ]);
    }
    
    public function getQueueStats()
    {
        $today = Carbon::today();
        
        // Get statistics
        $totalServed = Booking::where('status', 'completed')
            ->whereDate('end_time', $today)
            ->count();
            
        $averageServiceTime = Booking::where('status', 'completed')
            ->whereDate('end_time', $today)
            ->whereNotNull('actual_duration')
            ->avg('actual_duration') ?? 0;
            
        $currentQueueLength = Booking::where('status', 'queued')->count();
        
        $inProgress = Booking::where('status', 'in-progress')->count();
        
        return response()->json([
            'totalServed' => $totalServed,
            'averageServiceTime' => round($averageServiceTime),
            'currentQueueLength' => $currentQueueLength,
            'inProgress' => $inProgress
        ]);
        
    }

    public function getServerTime()
    {
        return response()->json([
            'time' => now()->toISOString()
        ]);
    }

    public function totalRevenue ()
    {
        return response()->json([
            //find service_id in bookings table and find price of service
            'revenue' => Booking::where('status', 'completed')->sum('service.price')
        ]);
    }

}