<?php

use App\Http\Controllers\DashboardController;
use App\Http\Middleware\SubdomainOrganizationMiddleware;
use App\Livewire\Organizations\CreateOrganization;
use App\Livewire\Organizations\EditOrganization;
use App\Livewire\Organizations\ShowOrganizations;
use App\Livewire\Users\CreateUser;
use App\Livewire\Users\EditUser;
use App\Livewire\Users\ShowUsers;
use App\Models\Event;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Subdomain Routes
Route::domain('{subdomain}.'.config('app.domain'))
    ->middleware(SubdomainOrganizationMiddleware::class)
    ->group(function () {
        // Public routes for subdomain
        Route::get('/', function (string $subdomain, Request $request) {
            return response()->json(Event::all());
        });

        // Auth routes for subdomain
        Route::middleware('guest')->group(function () {
            Volt::route('login', 'auth.login')
                ->name('subdomain.login');

            Volt::route('register', 'auth.register')
                ->name('subdomain.register');

            Volt::route('forgot-password', 'auth.forgot-password')
                ->name('subdomain.password.request');

            Volt::route('reset-password/{token}', 'auth.reset-password')
                ->name('subdomain.password.reset');
        });

        // Authenticated routes for subdomain
        Route::middleware(['auth'])->group(function () {
            Route::get('dashboard', [DashboardController::class, 'index'])
                ->name('subdomain.dashboard');

            // Other authenticated routes for subdomain...
        });
    });

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    Route::get('/organizations', ShowOrganizations::class)->name('organizations.index');
    Route::get('/organizations/create', CreateOrganization::class)->name('organizations.create');
    Route::get('/organizations/{organization}/edit', EditOrganization::class)->name('organizations.edit');

    Route::get('/users', ShowUsers::class)->name('users.index');
    Route::get('/users/create', CreateUser::class)->name('users.create');
    Route::get('/users/{user}/edit', EditUser::class)->name('users.edit');
});

require __DIR__ . '/auth.php';
