<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\NewPasswordController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RoleListController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PasswordResetLinkController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    Artisan::queue('test:command');
    return 'success';
})->name('home');

Route::get('/api', function () {
    return redirect()->route('home.api');
})->name('api');

Route::prefix('/admin')->group(function() {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [LoginController::class, 'index'])->name('admin.login');
        Route::post('/login', [LoginController::class, 'store'])->name('admin.login.store');
        Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
            ->name('admin.password.request');
        Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
            ->name('admin.password.email');
        Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
            ->name('admin.password.reset');
        Route::post('reset-password', [NewPasswordController::class, 'store'])
            ->name('admin.password.store');
    });

    Route::middleware('permission:view dashboard')->group(function() {

        Route::middleware(['auth', 'verified'])->group(function () {
            Route::get('/', function () {
                return view('dashboard');
            })->name('admin.dashboard')->can('view dashboard');

            Route::get('/profile', [ProfileController::class, 'edit'])->name('admin.profile.edit');
            Route::patch('/profile', [ProfileController::class, 'update'])->name('admin.profile.update');
            Route::put('password', [ProfileController::class, 'updatePassword'])->name('admin.password.update');
            Route::delete('/profile', [ProfileController::class, 'destroy'])->name('admin.profile.destroy');

            Route::controller(UserController::class)
                ->prefix('/users')
                ->group( function() {
                    Route::get('/add', 'create')
                        ->name('admin.users.create')
                        ->can('create user');
                    Route::get('/{user}', 'edit')
                        ->name('admin.users.edit')
                        ->can('update user');
                    Route::post('/', 'store')
                        ->name('admin.users.store')
                        ->can('create user');
                    Route::patch('/{user}', 'update')
                        ->name('admin.users.update')
                        ->can('update user');
                    Route::patch('/{user}/verify-email', 'verifyEmail')
                        ->name('admin.users.verify_email')
                        ->can('update user');
                    Route::get('/', 'index')
                        ->name('admin.users.index')
                        ->can('view users');
                    Route::delete('/{user}', 'destroy')
                        ->name('admin.users.destroy')
                        ->can('delete user');
                });

            Route::get('/roles', RoleListController::class)
                ->name('admin.roles.index')
                ->can('view roles');

            Route::get('/activity-logs', ActivityLogController::class)
                ->name('admin.logs.index')
                ->can('view logs');
        });

        Route::post('logout', [LoginController::class, 'destroy'])
            ->name('admin.logout');
    });
});


