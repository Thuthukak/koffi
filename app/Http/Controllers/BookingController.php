<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        //
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required',
            
        ]);

        $client = Client::create([
            'name' => $request->name,
            'phoneNumber' => 'phone_number', 
            'email' => 'email',
        ]);
        
        //not done
    }
}
