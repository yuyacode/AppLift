<?php

use App\Http\Controllers\CompanyInfoController;
use App\Http\Controllers\UserController;
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
            ->group(function () {
                Route::get('/', [CompanyInfoController::class, 'index'])->name('index');

                Route::prefix('basic_info')
                    ->name('basic_info.')
                    ->group(function () {
                        Route::get('/edit/{company_info}', [CompanyInfoController::class, 'edit'])->name('edit');
                        Route::post('/edit/{company_info}', [CompanyInfoController::class, 'update'])->name('update');
                    }
                );

                Route::prefix('member')
                    ->name('member.')
                    ->group(function () {
                        Route::get('/add', [UserController::class, 'create'])->name('create');
                        Route::post('/add', [UserController::class, 'store'])->name('store');
                    }
                );
            }
        );
    });

    require __DIR__.'/auth.php';
});