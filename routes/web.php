<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LogAccessController;
use App\Http\Controllers\LogChangeController;
use App\Http\Controllers\UserController; // <-- Agregar el controlador
use App\Http\Controllers\TablaController; // <-- Agregar el controlador
use App\Http\Controllers\GeneralController; // <-- Agregar el controlador
use App\Http\Controllers\InventoryController; // <-- Agregar el controlador
use App\Http\Controllers\MovementController; // <-- Agregar el controlador
use App\Http\Controllers\EquipmentController; // <-- Agregar el controlador
use App\Http\Controllers\ReportController;

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

    // Ruta para la vista de pestañas
    Route::get('/tablas', [HomeController::class, 'tablas'])->name('tablas.index');

    // Rutas CRUD unificadas para las 5 tablas
    Route::post('/tablas/{tabla}', [TablaController::class, 'store'])->name('tablas.store');
    Route::put('/tablas/{tabla}/{id}', [TablaController::class, 'update'])->name('tablas.update');
    Route::delete('/tablas/{tabla}/{id}', [TablaController::class, 'destroy'])->name('tablas.destroy');

    Route::put('/equipments/{equipment}', [EquipmentController::class, 'update'])->name('equipments.update');

    // Configuración General
    Route::get('/configuracion', [GeneralController::class, 'edit'])->name('general.edit');
    Route::put('/configuracion', [GeneralController::class, 'update'])->name('general.update');

    // Cambio de contraseña propio (nombres de ruta personalizados)
    Route::get('/mi-contrasena', [UserController::class, 'editPassword'])->name('profile.password');
    Route::put('/mi-contrasena', [UserController::class, 'updatePassword'])->name('profile.password.update');

    // Inventario y Movimientos
    Route::get('/inventario', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/movimientos', [MovementController::class, 'index'])->name('movements.index');
    Route::post('/movimientos', [MovementController::class, 'store'])->name('movements.store');
    Route::get('/movimientos/{movement}', [MovementController::class, 'show'])->name('movements.show');

    // Reportes Stock
    Route::get('/reportes/stock', [ReportController::class, 'stock'])->name('reports.stock');
    Route::get('/reportes/stock/pdf', [ReportController::class, 'stockPdf'])->name('reports.stock.pdf');
    Route::get('/reportes/stock/excel', [ReportController::class, 'stockExcel'])->name('reports.stock.excel');

    // Reporte Bajo Stock
    Route::get('/reportes/bajo-stock', [ReportController::class, 'lowStock'])->name('reports.low_stock');
    Route::get('/reportes/bajo-stock/pdf', [ReportController::class, 'lowStockPdf'])->name('reports.low_stock.pdf');
    Route::get('/reportes/bajo-stock/excel', [ReportController::class, 'lowStockExcel'])->name('reports.low_stock.excel');

    // Reporte Movimientos
    Route::get('/reportes/movimientos', [ReportController::class, 'movements'])->name('reports.movements');
    Route::get('/reportes/movimientos/pdf', [ReportController::class, 'movementsPdf'])->name('reports.movements.pdf');
    Route::get('/reportes/movimientos/excel', [ReportController::class, 'movementsExcel'])->name('reports.movements.excel');

    // Reporte Historial
    Route::get('/reportes/historial', [ReportController::class, 'history'])->name('reports.history');
    Route::get('/reportes/historial/pdf', [ReportController::class, 'historyPdf'])->name('reports.history.pdf');
    Route::get('/reportes/historial/excel', [ReportController::class, 'historyExcel'])->name('reports.history.excel');
});
