<?php

namespace App\Livewire\Backend;


use Dasundev\LivewireDropzone\Http\Livewire\Dropzone as BaseDropzone;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;

class ImprovedDropzone extends BaseDropzone
{
    #[Locked]
    public array $messages;
    public string $accentColor = '#138eff';
    public string $dbField;
    public function mount(array $rules = [], bool $multiple = false, array $files = [], array $messages = [], string $accentColor = '#138eff', string $dbField = ''): void
    {
        parent::mount($rules, $multiple, $files);
        $this->messages = $messages;
        $this->accentColor = $accentColor;
        $this->dbField = $dbField;
    }

    #[Computed]
    public function dimensions(): string
    {
        return collect($this->rules)
            ->filter(fn ($rule) => str_starts_with($rule, 'dimensions:'))
            ->flatMap(fn ($rule) => explode(',', substr($rule, strpos($rule, ':') + 1)))
            ->map(function ($dimension) {
                $parts = explode('=', trim($dimension));
                return [
                    'key' => $parts[0] ?? '',
                    'value' => $parts[1] ?? ''
                ];
            })
            ->filter(fn ($item) => !empty($item['value']))
            ->map(fn ($item) => match($item['key']) {
                'max_width', 'width' => $item['value'] . 'px',
                'max_height', 'height' => $item['value'] . 'px',
                default => $item['value']
            })
            ->values()
            ->join(' × ');
    }


    // just empty function so no error gets thrown
    public function markFileRemoved($dbField)
    {
    }
}
