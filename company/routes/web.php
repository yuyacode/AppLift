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

        Route::prefix('company_info')
            ->middleware('can:only-master')
            ->name('company_info.')
            ->controller(CompanyInfoController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/edit/{company_info}', 'edit')->name('edit');
        });

    });

    require __DIR__.'/auth.php';

});