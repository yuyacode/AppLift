<?php

use App\Http\Controllers\CompanyInfoController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('company')->group(function () {

    // Route::get('/', function () {
    //     return view('welcome');
    // });

    Route::middleware('auth')->group(function () {

        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        Route::get('/company_info', [CompanyInfoController::class, 'index'])->name('company_info.index');

    });

    require __DIR__.'/auth.php';

});