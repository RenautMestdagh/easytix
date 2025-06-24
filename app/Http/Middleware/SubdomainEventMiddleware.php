<?php

namespace App\Http\Middleware;

use App\Models\Event;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SubdomainEventMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the event subdomain
        $eventSubdomain = $request->route('eventsubdomain');

        // Find the event by subdomain
        $event = Event::where('subdomain', $eventSubdomain)->firstOrFail();

        // Add the eventuniqid to the route parameters
        $request->route()->setParameter('eventuniqid', $event->uniqid);

        return $next($request);
    }
}
