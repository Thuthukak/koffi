<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Barber;
use App\Models\Service;
use App\Models\Booking;
use App\Models\Client;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TestDataSeeder extends Seeder
{
    public function run()
{
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    
    DB::table('bookings')->truncate();
    DB::table('clients')->truncate();
    DB::table('barbers')->truncate();
    DB::table('services')->truncate();
    DB::table('users')->truncate();
    
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    $users = User::factory()->count(5)->create();
    $clients = $users->map(function ($user) {
        return Client::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'phoneNumber' => fake()->phoneNumber(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    });

    $barberCount = 2;
    $barbers = collect(range(1, $barberCount))->map(function ($index) {
        $email = 'barber'.$index.'@example.com';
        
        if (User::where('email', $email)->exists()) {
            $email = 'barber'.time().$index.'@example.com';
        }

        $user = User::create([
            'name' => 'Barber '.$index,
            'email' => $email,
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return Barber::create([
            'user_id' => $user->id,
            'phone' => '07123456'.(70 + $index),
            'bio' => 'Professional barber with '.($index + 3).' years experience',
            'experience' => $index + 3,
            'rating' => 4.5 + ($index * 0.2),
            'specialty' => $index === 0 ? 'Fade Cuts' : 'Beard Design',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    });

    $services = Service::insert([
        [
            'name' => 'Brush Fade',
            'duration' => 40,
            'price' => 100.00,
            'description' => 'Classic fade with brush finish',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'name' => 'Chiskop',
            'duration' => 30,
            'price' => 80.00,
            'description' => 'Clean clipper cut',
            'created_at' => now(),
            'updated_at' => now()
        ]
    ]);


    $bookings = [];
    $usedReferences = [];

    foreach ($clients as $client) {
        for ($i = 0; $i < 2; $i++) {
            do {
                $reference = Str::uuid();
            } while (in_array($reference, $usedReferences));

            $usedReferences[] = $reference;

            $bookings[] = [
                'client_id' => $client->id,
                'service_id' => rand(1, 2),
                'barber_id' => $barbers->random()->id,
                'bookingSlot' => Carbon::now()->addDays(rand(1,14))->setTime(10, 0),
                'status' => ['queued', 'in-progress', 'completed'][rand(0,2)],
                'reference' => $reference,
                'skipCount' => rand(0,2),
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
    }

    DB::table('bookings')->insert($bookings);

    $this->command->info('✅ Test data created successfully!');
    $this->command->table(
        ['Entity', 'Count'],
        [
            ['Users', User::count()],
            ['Clients', Client::count()],
            ['Barbers', Barber::count()],
            ['Services', Service::count()],
            ['Bookings', Booking::count()]
        ]
    );
}
}