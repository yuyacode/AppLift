<?php

use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::prefix('message')
        ->name('message.')
        ->group(function () {
            Route::prefix('access-token')
                ->name('access-token.')
                ->group(function () {
                    Route::get('/', [MessageController::class, 'get_access_token'])->name('get');
                    Route::post('/refresh', [MessageController::class, 'refresh_access_token'])->name('refresh');
                }
            );
        }
    );
});
