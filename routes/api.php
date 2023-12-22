<?php

use App\Http\Controllers\v1\ProfileController;
use App\Http\Controllers\v1\ProfileSubscriptionController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::controller(ProfileController::class)
        ->prefix('/profile')
        ->group(function () {
            Route::get('/', 'show')->name('profile.show');
            Route::patch('/', 'update')->name('profile.update');
            Route::delete('/', 'destroy')->name('profile.destroy');
        });
    Route::get('/profile/subscription', ProfileSubscriptionController::class)
        ->name('profile.subscription');
});

Route::get('/', function () {
    Artisan::queue('user:create');
    return ['this-is-laravel-api' => app()->version()];
})->name('home.api');

require __DIR__ . '/auth.php';
