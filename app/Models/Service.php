<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'description',
        'price',
        'duration',
        'photo'
    ];

    /**
     * Get the photo URL attribute
     * This ensures we always return a full URL for the photo
     */
    public function getPhotoUrlAttribute()
    {
        if ($this->photo) {
            // If photo starts with http, it's already a full URL
            if (str_starts_with($this->photo, 'http')) {
                return $this->photo;
            }
            // Otherwise, prepend the app URL
            return url($this->photo);
        }
        
        return null;
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
