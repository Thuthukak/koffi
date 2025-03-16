<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;

class ServicesController extends Controller
{
    public function index()
    {
        return Service::all()->map(function($service) {
            return [
                'id' => $service->id,
                'name' => $service->name,
                'duration' => $service->duration,
                'price' => number_format($service->price, 2),
                'description' => $service->description
            ];
        });
    }
}
