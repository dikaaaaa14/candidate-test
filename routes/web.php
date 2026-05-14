<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CltLayupController;
use App\Http\Controllers\CltLayerController;
use Illuminate\Support\Facades\Route;

Route::resource('suppliers', SupplierController::class);

Route::get('/', function () {
    return view('welcome');
});

Route::get('/suppliers/{supplier}/export', [SupplierController::class, 'export'])->name('suppliers.export');
Route::get('/suppliers/{supplier}/import', [SupplierController::class, 'importForm'])->name('suppliers.import.form');
Route::post('/suppliers/{supplier}/import', [SupplierController::class, 'import'])->name('suppliers.import');

Route::resource('suppliers', SupplierController::class);
Route::resource('suppliers.layups', CltLayupController::class);
Route::resource('suppliers.layups.layers', CltLayerController::class);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/suppliers/{supplier}/export', [SupplierController::class, 'export'])->name('suppliers.export');
Route::get('/suppliers/{supplier}/import', [SupplierController::class, 'importForm'])->name('suppliers.import.form');
Route::post('/suppliers/{supplier}/import', [SupplierController::class, 'import'])->name('suppliers.import');

require __DIR__.'/auth.php';
