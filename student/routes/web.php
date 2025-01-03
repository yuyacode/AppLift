<?php

use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('student')->group(function () {
    Route::middleware('auth')->group(function () {

        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');

        Route::prefix('profile')
            ->name('profile.')
            ->controller(ProfileController::class)
            ->group(function () {
                Route::get('/', 'edit')->name('edit');
                Route::patch('/', 'update')->name('update');
                Route::delete('/', 'destroy')->name('destroy');
            }
        );

        Route::prefix('message')
            ->name('message.')
            ->group(function () {
                Route::get('/', [MessageController::class, 'index'])->name('index');
            }
        );
    });

    require __DIR__.'/auth.php';
    require __DIR__.'/api.php';
});