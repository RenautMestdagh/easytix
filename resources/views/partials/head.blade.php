<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
@if(request()->routeIs('home'))
    {{ config('app.name') }}
@else
    {{ isset($organization) ? $organization->name . ' - Easytix' : ($title ?? config('app.name')) }}
@endif
</title>

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

<!-- Favicon implementation -->
@if(isset($organization) && $organization->favicon_url)
    <link rel="icon" href="{{ $organization->favicon_url }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ $organization->favicon_url }}" type="image/x-icon">
    <link rel="apple-touch-icon" href="{{ $organization->favicon_url }}">
@else
    <link rel="icon" href="{{ asset('logo.png') }}" type="image/png">
    <link rel="shortcut icon" href="{{ asset('logo.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('logo.png') }}">
@endif

@stack('scripts')
@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
