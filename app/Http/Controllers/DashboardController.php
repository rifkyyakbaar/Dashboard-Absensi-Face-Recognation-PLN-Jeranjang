<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // panggil view 'dashboard.blade.php'
        return view('dashboard'); 
    }
}