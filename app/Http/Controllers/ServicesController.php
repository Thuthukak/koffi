<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use Illuminate\Support\Facades\Route;

class ServicesController extends Controller
{
    public function index()
    {
        return response()->json(Service::all());
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => ['required', 'integer'],          
            'duration' => ['required', 'integer']
        ]);
    
        $service = Service::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'duration' => $request->duration
        ]);
    
        return response()->json([
            'success' => true,
            'message' => 'Service created successfully',
            'service' => $service
        ]);
    }
    

}