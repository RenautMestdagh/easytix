<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\TicketController;
use App\Http\Middleware\CheckPermissionMiddleware;
use App\Http\Middleware\SubdomainOrganizationMiddleware;
use App\Livewire\Backend\Discountcodes\CreateDiscountCode;
use App\Livewire\Backend\Discountcodes\EditDiscountCode;
use App\Livewire\Backend\Discountcodes\ShowDiscountCodes;
use App\Livewire\Backend\Events\CreateEvent;
use App\Livewire\Backend\Events\EditEvent;
use App\Livewire\Backend\Events\ShowEvents;
use App\Livewire\Backend\Organizations\CreateOrganization;
use App\Livewire\Backend\Organizations\EditOrganization;
use App\Livewire\Backend\Organizations\ShowOrganizations;
use App\Livewire\Backend\Organizations\UploadMedia;
use App\Livewire\Backend\Tickettypes\CreateTicketType;
use App\Livewire\Backend\Tickettypes\EditTicketType;
use App\Livewire\Backend\Tickettypes\ShowTypes;
use App\Livewire\Backend\Users\CreateUser;
use App\Livewire\Backend\Users\EditUser;
use App\Livewire\Backend\Users\ShowUsers;
use App\Livewire\Frontend\EventCheckout;
use App\Livewire\Frontend\EventPayment;
use App\Livewire\Frontend\EventTicketsSelector;
use App\Livewire\Frontend\PaymentConfirmation;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Subdomain Routes
Route::domain('{subdomain}.'.config('app.domain'))
    ->middleware(SubdomainOrganizationMiddleware::class)
    ->group(function () {
        // Public routes for subdomain
        Route::get('/', [OrganizationController::class, 'show'])->name('organization.home');

        Route::get('/event/{eventuniqid}', EventTicketsSelector::class)->name('event.tickets');
        Route::get('/event/{eventuniqid}/checkout', EventCheckout::class)->name('event.checkout');
        Route::get('/event/{eventuniqid}/payment', EventPayment::class)->name('event.payment');
        Route::get('/event/{eventuniqid}/payment/confirmation', PaymentConfirmation::class)->name('stripe.payment.confirmation');

        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('/tickets/{order}/download', [TicketController::class, 'download'])->name('tickets.download');
    });

// Main domain routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Main domain guest routes
Route::middleware(['guest', SubdomainOrganizationMiddleware::class])->group(function () {
    Volt::route('login', 'auth.login')
        ->name('login');

    Volt::route('forgot-password', 'auth.forgot-password')
        ->name('password.request');

    Volt::route('reset-password/{token}', 'auth.reset-password')
        ->name('password.reset');
});

// Main domain auth routes
Route::middleware(['auth', 'verified', CheckPermissionMiddleware::class, SubdomainOrganizationMiddleware::class])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    Route::get('/organizations', ShowOrganizations::class)->name('organizations.index');
    Route::get('/organizations/create', CreateOrganization::class)->name('organizations.create');
    Route::get('/organizations/{organization}/edit', EditOrganization::class)->name('organizations.update');
    Route::get('/organizations/{organization}/media', UploadMedia::class)->name('organizations.media');

    Route::get('/users', ShowUsers::class)->name('users.index');
    Route::get('/users/create', CreateUser::class)->name('users.create');
    Route::get('/users/{user}/edit', EditUser::class)->name('users.update');
    Route::post('/switch-back', [ShowUsers::class, 'switchBackToOriginalUser'])->name('login-as.use');

    Route::get('/events', ShowEvents::class)->name('events.index');
    Route::get('/events/create', CreateEvent::class)->name('events.create');
    Route::get('/events/{event}/edit', EditEvent::class)->name('events.update');

    Route::get('/events/{event}/tickets', ShowTypes::class)->name('ticket-types.index');
    Route::get('/events/{event}/tickets/create', CreateTicketType::class)->name('ticket-types.create');
    Route::get('/events/{event}/ticket-types/{ticketType}/edit', EditTicketType::class)->name('ticket-types.update');

    Route::get('/discount-codes', ShowDiscountCodes::class)->name('discount-codes.index');
    Route::get('/discount-codes/create', CreateDiscountCode::class)->name('discount-codes.create');
    Route::get('/discount-codes/{discountCode}/edit', EditDiscountCode::class)->name('discount-codes.update');

    Route::get('/ticketscanner', [ScanController::class, 'show'])->name('scanner.show');
    Route::post('/scan-ticket', [ScanController::class, 'scan'])->name('scanner.use');
    // to here should go with CheckPermissionMiddleware
});

require __DIR__ . '/auth.php';

if(!app()->environment('local'))
    Route::fallback(function () {
        return redirect('/');
    });
