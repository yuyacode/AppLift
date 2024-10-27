<?php

use App\Http\Controllers\CompanyInfoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('company')->group(function () {
    Route::middleware('auth')->group(function () {

        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');

        Route::prefix('profile')
            ->name('profile.')
            ->controller(ProfileController::class)
            ->group(function () {
                Route::get('/', 'edit')->name('edit');
                Route::patch('/', 'update_account_info')->name('update_account_info');
                Route::patch('/{user}', 'update_profile_info')->name('update_profile_info');
                Route::delete('/', 'destroy')->name('destroy');
            }
        );

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

        Route::prefix('review')
            ->name('review.')
            ->group(function () {
                Route::get('/', [ReviewController::class, 'edit'])->name('edit');
                Route::post('/{reviews}', [ReviewController::class, 'update'])->name('update');
            }
        );

    });

    require __DIR__.'/auth.php';
});