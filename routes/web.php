<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LogAccessController;
use App\Http\Controllers\LogChangeController;
use App\Http\Controllers\UserController; // <-- Agregar el controlador

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

// Ruta protegida que pasa el rol a la vista
Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware('auth');

// Rutas protegidas
Route::middleware(['auth'])->group(function () {
    // Log de accesos
    Route::get('/log-access', [LogAccessController::class, 'index'])->name('log-access.index');
    Route::post('/log-access', [LogAccessController::class, 'store'])->name('log-access.store');

    // Usuarios (Resource) - CORRECCIÓN AQUÍ
    Route::resource('usuarios', UserController::class)->parameter('usuarios', 'user');

    // Log de cambios <-- AGREGAR ESTA LÍNEA
    Route::get('/log-change', [LogChangeController::class, 'index'])->name('log-change.index');
});
