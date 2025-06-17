<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
    @stack('styles')
</head>
<body class="min-h-screen bg-gray-50 dark:bg-zinc-800 flex flex-col">

<div class="flex-1">
    @if(isset($backgroundOverride) ? $backgroundOverride : $organization->background_image)
        <div class="fixed inset-0 z-0 bg-cover bg-center" style="background-image: url('{{ isset($backgroundOverride) ? $backgroundOverride : $organization->background_url }}');"></div>
    @endif

    <!-- Header with Logo -->
    @if(isset($logoOverride) ? $logoOverride : $organization->logo)
        <div class="relative z-10 mx-auto px-4 py-6">
            <div class="h-20 flex justify-center">
                <img src="{{ isset($logoOverride) ? $logoOverride : $organization->logo_url }}"
                     alt="{{ $organization->name }}"
                     class="h-full object-contain">
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8 relative z-10">
        {{ $slot }}
    </main>
</div>

<!-- Footer -->
@include('components.layouts.app.footer')

@stack('scripts')
</body>
</html>
