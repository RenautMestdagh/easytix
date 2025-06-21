<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Organization;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function show(Request $request)
    {
        $organization = Organization::find(session('organization_id')); // injected by SubdomainOrganizationMiddleware
        $events = Event::with('venue')->where('is_published', true)
            ->where('date', '>=', now()->format('Y-m-d'))
            ->orderBy('date')
            ->get();

        return view('frontend.homepage', compact('organization', 'events'));
    }
}
