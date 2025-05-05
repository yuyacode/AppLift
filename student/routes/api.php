<?php

use App\Http\Controllers\CompanyInfoController;
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
            Route::prefix('proxy')
                ->name('proxy.')
                ->group(function () {
                    Route::get('{path?}', [MessageController::class, 'proxyGet'])
                        ->where('path', '.*');
                    Route::post('{path?}', [MessageController::class, 'proxyPost'])
                        ->where('path', '.*');
                    Route::patch('{path?}', [MessageController::class, 'proxyPatch'])
                        ->where('path', '.*');
                    Route::delete('{path?}', [MessageController::class, 'proxyDelete'])
                        ->where('path', '.*');
            });
        }
    );

    Route::prefix('company_info')
        ->name('company_info.')
        ->controller(CompanyInfoController::class)
        ->group(function () {
            Route::get('/search', 'search')->name('search');
        }
    );
});
