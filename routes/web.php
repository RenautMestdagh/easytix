<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\TicketController;
use App\Http\Middleware\CheckPermissionMiddleware;
use App\Http\Middleware\SubdomainEventMiddleware;
use App\Livewire\Backend\DiscountCodes\CreateDiscountCode;
use App\Livewire\Backend\DiscountCodes\EditDiscountCode;
use App\Livewire\Backend\DiscountCodes\ShowDiscountCodes;
use App\Livewire\Backend\Events\CreateEvent;
use App\Livewire\Backend\Events\EditEvent;
use App\Livewire\Backend\Events\ShowEvents;
use App\Livewire\Backend\Events\ShowStats;
use App\Livewire\Backend\Organizations\CreateOrganization;
use App\Livewire\Backend\Organizations\EditOrganization;
use App\Livewire\Backend\Organizations\ShowOrganizations;
use App\Livewire\Backend\Organizations\UploadMedia;
use App\Livewire\Backend\ShowRevenue;
use App\Livewire\Backend\TicketTypes\CreateTicketType;
use App\Livewire\Backend\TicketTypes\EditTicketType;
use App\Livewire\Backend\TicketTypes\ShowTypes;
use App\Livewire\Backend\Users\CreateUser;
use App\Livewire\Backend\Users\EditUser;
use App\Livewire\Backend\Users\ShowUsers;
use App\Livewire\Backend\Venues\CreateVenue;
use App\Livewire\Backend\Venues\EditVenue;
use App\Livewire\Backend\Venues\ShowVenues;
use App\Livewire\Frontend\HelpCenter;
use App\Livewire\Frontend\HomeController;
use App\Livewire\Frontend\OrderCheckout;
use App\Livewire\Frontend\OrderConfirmation;
use App\Livewire\Frontend\OrderPayment;
use App\Livewire\Frontend\TicketSelection;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;


// Subdomain Routes
Route::domain('{subdomain}.' . config('app.domain'))->group(function () {
    // Public routes for subdomain
    Route::get('/', [OrganizationController::class, 'show'])->name('organization.home');

    Route::get('/event/{eventuniqid}', TicketSelection::class)->name('event.tickets');
    Route::get('/event/{eventuniqid}/checkout', OrderCheckout::class)->name('event.checkout');
    Route::get('/event/{eventuniqid}/payment', OrderPayment::class)->name('event.payment');
    Route::get('/event/{eventuniqid}/payment/confirmation', OrderConfirmation::class)->name('stripe.payment.confirmation');

    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/tickets/{order}/download', [TicketController::class, 'download'])->name('tickets.download');
});


Route::domain('{eventsubdomain}.{subdomain}.' . config('app.domain'))->middleware([SubdomainEventMiddleware::class,])->group(function () {
    Route::get('/', TicketSelection::class)->name('event.subdomain.tickets');
    Route::get('/checkout', OrderCheckout::class)->name('event.subdomain.checkout');
    Route::get('/payment', OrderPayment::class)->name('event.subdomain.payment');
    Route::get('/payment/confirmation', OrderConfirmation::class)->name('stripe.subdomain.payment.confirmation');
});

// Main domain routes
Route::get('/', HomeController::class)->name('home');
Route::get('/help', HelpCenter::class)->name('help');

// Main domain guest routes
Route::middleware(['guest'])->group(function () {
    Volt::route('login', 'auth.login')->name('login');
    Volt::route('forgot-password', 'auth.forgot-password')->name('password.request');
    Volt::route('reset-password/{token}', 'auth.reset-password')->name('password.reset');
});

// Main domain auth routes
Route::middleware(['auth', 'verified', CheckPermissionMiddleware::class])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    Route::get('/organizations', ShowOrganizations::class)->name('organizations.index');
    Route::get('/organizations/create', CreateOrganization::class)->name('organizations.create');
    Route::get('/organization/edit', EditOrganization::class)->name('organizations.update');

    Route::get('/organization/media', UploadMedia::class)->name('organizations.media');
    Route::get('/revenue', ShowRevenue::class)->name('revenue.index');

    Route::get('/users', ShowUsers::class)->name('users.index');
    Route::get('/users/create', CreateUser::class)->name('users.create');
    Route::get('/users/{user}/edit', EditUser::class)->name('users.update');
    Route::post('/switch-back', [ShowUsers::class, 'switchBackToOriginalUser'])->name('login-as.use');

    Route::get('/venues', ShowVenues::class)->name('venues.index');
    Route::get('/venues/create', CreateVenue::class)->name('venues.create');
    Route::get('/venues/{venue}/edit', EditVenue::class)->name('venues.update');

    Route::get('/events', ShowEvents::class)->name('events.index');
    Route::get('/events/create', CreateEvent::class)->name('events.create');
    Route::get('/events/{event}/edit', EditEvent::class)->name('events.update');
    Route::get('/events/{event}/stats', ShowStats::class)->name('events.index.stats');

    Route::get('/events/{event}/tickets', ShowTypes::class)->name('ticket-types.index');
    Route::get('/events/{event}/tickets/create', CreateTicketType::class)->name('ticket-types.create');
    Route::get('/events/{event}/ticket-types/{ticketType}/edit', EditTicketType::class)->name('ticket-types.update');

    Route::get('/discount-codes', ShowDiscountCodes::class)->name('discount-codes.index');
    Route::get('/discount-codes/create', CreateDiscountCode::class)->name('discount-codes.create');
    Route::get('/discount-codes/{discountCode}/edit', EditDiscountCode::class)->name('discount-codes.update');

    Route::get('/ticketscanner', [ScanController::class, 'show'])->name('scanner.show');
    Route::post('/scan-ticket', [ScanController::class, 'scan'])->name('scanner.use');
});

require __DIR__ . '/auth.php';

if(!app()->environment('local'))
    Route::fallback(function () {
        return redirect('/');
    });
