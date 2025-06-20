<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\DiscountCode;
use App\Models\Organization;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Event;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'organizationsCount' => Organization::count(),
            'usersCount' => User::count(),
            'eventsCount' => Event::count(),
            'discountCodesCount' => DiscountCode::count(),
            'ticketsCount' => Ticket::count(), // Add this line for total tickets
            'customersCount' => Customer::count(), // Add this line for total tickets
        ]);
    }
}
