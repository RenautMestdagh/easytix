<?php

use App\Http\Middleware\SubdomainOrganizationMiddleware;
use App\Livewire\Organizations\CreateOrganization;
use App\Livewire\Organizations\EditOrganization;
use App\Livewire\Organizations\ShowOrganization;
use App\Models\Event;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::domain('{subdomain}.'.config('app.domain'))
    ->middleware(SubdomainOrganizationMiddleware::class)
    ->group(function () {
        Route::get('/', function (string $subdomain, Request $request) {
            return response()->json(Event::all());
//            return view('welcome');
        });

        // Your other routes can access $request->organization_id directly
    });

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    Route::get('/organizations', ShowOrganization::class)->name('organizations.index');
    Route::get('/organizations/create', CreateOrganization::class)->name('organizations.create');
    Route::get('/organizations/{organization}/edit', EditOrganization::class)->name('organizations.edit');
});

require __DIR__ . '/auth.php';
