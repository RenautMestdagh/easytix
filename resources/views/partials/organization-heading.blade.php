@if($organization->background_image)
    <div class="fixed inset-0 z-0 bg-cover bg-center" style="background-image: url('{{ $organization->background_url }}');"></div>
@endif

@if($organization->logo)
    <div class="{{ isset($logoClass) ? $logoClass : 'h-32 mb-16' }} relative z-10">
        <img src="{{ $organization->logo_url }}" alt="{{ $organization->name }}" class="w-full h-full object-contain">
    </div>
@endif
