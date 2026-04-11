<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/laporan', function () {
    return view('laporan');
})->name('laporan');

use App\Http\Controllers\DatabaseController;
Route::get('/backup-database', [DatabaseController::class, 'backup']);


