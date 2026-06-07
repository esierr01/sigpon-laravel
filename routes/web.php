<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;

use App\Http\Controllers\LogAccessController;

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

// Ruta protegida que pasa el rol a la vista
Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware('auth');

// Rutas para el log de accesos (protegidas)
Route::middleware(['auth'])->group(function () {
    Route::get('/log-access', [LogAccessController::class, 'index'])->name('log-access.index');
    Route::post('/log-access', [LogAccessController::class, 'store'])->name('log-access.store');
});
