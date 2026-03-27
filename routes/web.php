<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    $user = $request->user();

    if ($user !== null) {
        return redirect()->route($user->isAdmin() ? 'admin.portal' : 'dashboard');
    }

    return view('home');
})->name('home');

Route::get('/dashboard', function (Request $request) {
    $user = $request->user();

    if ($user !== null && $user->isAdmin()) {
        return redirect()->route('admin.portal');
    }

    return view('dashboard');
})
    ->middleware(['auth'])
    ->name('dashboard');

Route::get('/verify-email/{id}/{hash}', EmailVerificationController::class)
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.public');

Route::middleware(['auth', EnsureUserIsAdmin::class])
    ->prefix('admin-portal')
    ->name('admin.')
    ->group(function (): void {
        Route::view('/', 'admin-portal')->name('portal');

        Route::prefix('users')
            ->name('users.')
            ->group(function (): void {
                Route::get('/', [UserController::class, 'index'])->name('index');
                Route::get('/create', [UserController::class, 'create'])->name('create');
                Route::post('/', [UserController::class, 'store'])->name('store');
                Route::get('/export', [UserController::class, 'export'])->name('export');
                Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
                Route::get('/{user}/confirm-delete', [UserController::class, 'confirmDestroy'])->name('confirm-delete');
                Route::put('/{user}', [UserController::class, 'update'])->name('update');
                Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
            });
    });

require __DIR__.'/settings.php';
