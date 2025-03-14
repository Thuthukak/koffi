<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Log;

class DashboardController extends Controller
{
    public function index()
    {
        Log::info('returning admin dashboard blade');

        return view('admin.dashboard');
    }
}
