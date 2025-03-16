<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'phoneNumber' => $this->faker->phoneNumber,
            'name' => $this->faker->name,
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}