<?php

namespace App\Http\Middleware;

use App\Models\Organization;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SubdomainOrganizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $subdomain = $request->route('subdomain');

        // Find the organization by subdomain
        $organization = Organization::where('subdomain', $subdomain)->first();

        if (!$organization) {
            abort(404, 'Organization not found');
        }

        // Share the organization id with the request
        session(['organization_id' => $organization->id]);

        return $next($request);
    }
}
