<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organization;
use App\Models\Event;

class OrganizationController extends Controller
{
    public function show(Request $request)
    {
        $organization = Organization::find(session('organization_id')); // injected by SubdomainOrganizationMiddleware
        $events = Event::where('is_published', true)
            ->where('date', '>=', now()->format('Y-m-d'))
            ->orderBy('date')
            ->get();

        return view('frontend.homepage', compact('organization', 'events'));
    }
}
