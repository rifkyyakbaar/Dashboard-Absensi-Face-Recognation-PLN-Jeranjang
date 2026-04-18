<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index()
    {
        // Fungsi ini bertugas memanggil file view 'laporan.blade.php'
        return view('laporan'); 
    }
}