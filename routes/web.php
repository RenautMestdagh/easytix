<?php

use App\Http\Controllers\DashboardController;
use App\Http\Middleware\SubdomainOrganizationMiddleware;
use App\Livewire\Organizations\CreateOrganization;
use App\Livewire\Organizations\EditOrganization;
use App\Livewire\Organizations\ShowOrganizations;
use App\Livewire\Users\CreateUser;
use App\Livewire\Users\EditUser;
use App\Livewire\Users\ShowUsers;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Define authenticated routes for both main domain and subdomains
$authRoutes = function () {
    Route::get('dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

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
};

// Guest/login routes for both main domain and subdomains
$guestRoutes = function () {
    Volt::route('login', 'auth.login')
        ->name('login');

    Volt::route('forgot-password', 'auth.forgot-password')
        ->name('password.request');

    Volt::route('reset-password/{token}', 'auth.reset-password')
        ->name('password.reset');
};

// Subdomain Routes
Route::domain('{subdomain}.'.config('app.domain'))
    ->middleware(SubdomainOrganizationMiddleware::class)
    ->group(function () use ($authRoutes, $guestRoutes) {
        // Public routes for subdomain
        // Route::get('/', function (string $subdomain, Request $request) {
        //     return response()->json(Event::all());
        // });

        // Guest routes with subdomain naming
        Route::middleware('guest')->group($guestRoutes);

        // Auth routes for subdomain
        Route::middleware(['auth', 'verified'])->group($authRoutes);
    });

// Main domain routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Main domain guest routes
Route::middleware('guest')->group($guestRoutes);

// Main domain auth routes
Route::middleware(['auth', 'verified'])->group($authRoutes);

require __DIR__ . '/auth.php';
