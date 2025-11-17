<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CarController;

// Main page
Route::get('/', function () {
    return view('cars.index');
})->name('cars.index');

// API routes for cars
Route::prefix('api/cars')->group(function () {
    Route::get('/', [CarController::class, 'index'])->name('api.cars.index');
    Route::post('/', [CarController::class, 'store'])->name('api.cars.store');
    Route::get('/{id}', [CarController::class, 'show'])->name('api.cars.show');
    Route::put('/{id}', [CarController::class, 'update'])->name('api.cars.update');
    Route::delete('/{id}', [CarController::class, 'destroy'])->name('api.cars.destroy');
    Route::post('/{id}/move', [CarController::class, 'move'])->name('api.cars.move');
});
