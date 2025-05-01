<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\User;
use App\Models\Event; // Add this line
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'organizationsCount' => Organization::count(),
            'superadminsCount' => User::role('superadmin')->count(),
            'adminsCount' => User::role('admin')->count(),
            'organizersCount' => User::role('organizer')->count(),
            'eventsCount' => Event::count(), // Add this line
        ]);
    }
}
