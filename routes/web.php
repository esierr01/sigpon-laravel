<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

// Ruta protegida que pasa el rol a la vista
Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware('auth');
