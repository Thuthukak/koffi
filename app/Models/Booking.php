<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Barber;
use App\Models\Service;
use App\Models\Client;
use App\Models\Reminder;
use Illuminate\Database\Eloquent\SoftDeletes;


class Booking extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'client_id',
        'reference',
        'service_id',
        'barber_id',
        'bookingSlot',
        'status',
        'skipCount',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
    public function incrementSkips() {
        $this->increment('skipCount');
    }
    
    public function canRebook() {
        return $this->skipCount >= 3;
    }

    // Add these methods to Booking model
public function scopeUpcoming($query)
{
    return $query->where('bookingSlot', '>', now())
                 ->whereNotIn('status', ['completed', 'canceled']);
}

public function getFormattedDateAttribute()
{
    return $this->bookingSlot->format('D, M j H:i');
}

}
