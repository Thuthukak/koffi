<?php

namespace App\Models;

use App\Notifications\BookingConfirmation;
use App\Notifications\Reminder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class client extends Model
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'name',
        'phoneNumber',
        'status', //in queue, skipped or done cutting
        'skipCount', //in queue, skipped or done cutting
        'password',
    ];

    public function numberOfSkips() {
        return $this->skipCount >= 1;
        $this->increment('skipCount');
    }
    //Function to confirm a clients booking
    public function bookingComfirmation() {
        $this->notify(new BookingConfirmation($this));
    }
    //send the client a reminder for thier booking
    public function sendReminder() {
        $this->notify(new Reminder($this));
    }
    //if a client has been skipped more 3  or more times they have the option to rebook for another slot
    public function rebook() {
        $this->update([
            'skipCount' => 3,
            'status' => 'queued',
            'bookingSlot' => now(),
        ]);
    }



    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
