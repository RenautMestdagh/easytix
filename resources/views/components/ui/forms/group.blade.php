@props([
    'label' => null,
    'for' => null,
    'error' => null,
])

<div class="mb-4">
    @if($label)
        <x-ui.forms.label :for="$for">{{ $label }}</x-ui.forms.label>
    @endif

    {{ $slot }}

    @if($error)
        <x-ui.forms.error :error="$error" />
    @endif
</div>

