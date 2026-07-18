<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CocController; 
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CtplIssuanceController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

// Pangkat ng mga Routes na nangangailangan ng Login at Verified Email
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard Route
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // COC Management Route
    Route::get('/coc-management', [CocController::class, 'index'])->name('coc.index');
    Route::post('/coc-management', [CocController::class, 'store'])->name('coc.store');
    Route::get('/check-coc-availability', [CocController::class, 'checkAvailability']);
    Route::delete('/coc-management/delete-series', [CocController::class, 'deleteSeries']);

    //CTPL Route
    Route::get('/ctpl-issuance', [CtplIssuanceController::class, 'create'])->name('ctpl.issuance');
    Route::post('/ctpl-issuance', [CtplIssuanceController::class, 'store'])->name('ctpl.issuance.store');
    
});

// Pangkat ng mga Routes para sa Profile Management (Auth lamang)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';