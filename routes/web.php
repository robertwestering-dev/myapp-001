<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route(auth()->user()->isAdmin() ? 'admin.portal' : 'dashboard');
    }

    return view('home');
})->name('home');

Route::get('/dashboard', function () {
    if (auth()->user()->isAdmin()) {
        return redirect()->route('admin.portal');
    }

    return view('dashboard');
})
    ->middleware(['auth'])
    ->name('dashboard');

Route::get('/verify-email/{id}/{hash}', EmailVerificationController::class)
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.public');

Route::view('/admin-portal', 'admin-portal')
    ->middleware(['auth', EnsureUserIsAdmin::class])
    ->name('admin.portal');

Route::get('/admin-portal/users', [UserController::class, 'index'])
    ->middleware(['auth', EnsureUserIsAdmin::class])
    ->name('admin.users.index');

Route::get('/admin-portal/users/create', [UserController::class, 'create'])
    ->middleware(['auth', EnsureUserIsAdmin::class])
    ->name('admin.users.create');

Route::post('/admin-portal/users', [UserController::class, 'store'])
    ->middleware(['auth', EnsureUserIsAdmin::class])
    ->name('admin.users.store');

Route::get('/admin-portal/users/{user}/edit', [UserController::class, 'edit'])
    ->middleware(['auth', EnsureUserIsAdmin::class])
    ->name('admin.users.edit');

Route::put('/admin-portal/users/{user}', [UserController::class, 'update'])
    ->middleware(['auth', EnsureUserIsAdmin::class])
    ->name('admin.users.update');

Route::delete('/admin-portal/users/{user}', [UserController::class, 'destroy'])
    ->middleware(['auth', EnsureUserIsAdmin::class])
    ->name('admin.users.destroy');

Route::get('/admin-portal/users/export', [UserController::class, 'export'])
    ->middleware(['auth', EnsureUserIsAdmin::class])
    ->name('admin.users.export');

require __DIR__.'/settings.php';
