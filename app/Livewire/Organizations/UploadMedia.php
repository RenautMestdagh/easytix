<?php

namespace App\Livewire\Organizations;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Organization;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\OrganizationMediaRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class UploadMedia extends Component
{
    use WithFileUploads;

    public Organization $organization;
    public $favicon;
    public $logo;
    public $background;

    public function mount(Organization $organization)
    {
        $this->organization = $organization;
    }

    public function save()
    {
        $this->validateRequest();

        if ($this->logo) {
            $this->uploadLogo();
        }

        if ($this->background) {
            $this->uploadBackground();
        }

        if ($this->favicon) { // Added favicon upload
            $this->uploadFavicon();
        }

        if ($this->logo || $this->background || $this->favicon) {
            session()->flash('message', __('Media updated successfully.'));
            $this->dispatch('flash-message');
        }
        $this->reset(['logo', 'background', 'favicon']);
    }

    public function saveLogo()
    {
        $this->validateRequest('logo');

        if ($this->logo) {
            $this->uploadLogo();
            $this->dispatch('notify',
                type: 'success',
                content: 'Logo updated successfully'
            );
            $this->reset('logo');
        }
    }

    public function saveBackground()
    {
        $this->validateRequest('background');

        if ($this->background) {
            $this->uploadBackground();
            $this->dispatch('notify',
                type: 'success',
                content: 'Background updated successfully'
            );
            $this->reset('background');
        }
    }

    protected function validateRequest($field = null)
    {
        $request = new OrganizationMediaRequest();

        if ($field) {
            $rules = [$field => $request->rules()[$field]];
            $data = [$field => $this->{$field}];
        } else {
            $rules = $request->rules();
            $data = [
                'logo' => $this->logo,
                'background' => $this->background,
                'favicon' => $this->favicon // Added favicon
            ];
        }

        Validator::make($data, $rules, $request->messages(), $request->attributes())->validate();
    }

    protected function uploadFavicon()
    {
        $this->deleteExistingFavicon();

        $this->favicon->storeAs(
            "organizations/{$this->organization->id}",
            'favicon.'.$this->favicon->extension(),
            'public'
        );
    }

    protected function uploadLogo()
    {
        $this->deleteExistingLogo();

        $this->logo->storeAs(
            "organizations/{$this->organization->id}",
            'logo.'.$this->logo->extension(),
            'public'
        );
    }

    protected function uploadBackground()
    {
        $this->deleteExistingBackground();

        $this->background->storeAs(
            "organizations/{$this->organization->id}",
            'background.'.$this->background->extension(),
            'public'
        );
    }

    public function removeFavicon()
    {
        $this->deleteExistingFavicon();
        session()->flash('message', __('Favicon removed successfully.'));
        $this->dispatch('flash-message');
    }

    public function removeLogo()
    {
        $this->deleteExistingLogo();
        session()->flash('message', __('Logo removed successfully.'));
        $this->dispatch('flash-message');
    }

    public function removeBackground()
    {
        $this->deleteExistingBackground();
        session()->flash('message', __('Background removed successfully.'));
        $this->dispatch('flash-message');
    }

    protected function deleteExistingFavicon()
    {
        $directory = "organizations/{$this->organization->id}";

        if (Storage::disk('public')->exists($directory)) {
            $files = Storage::disk('public')->files($directory);

            foreach ($files as $file) {
                if (preg_match('/^favicon\.(png|ico)$/i', basename($file))) {
                    Storage::disk('public')->delete($file);
                }
            }
        }
    }

    protected function deleteExistingLogo()
    {
        // Use the 'public' disk explicitly
        $directory = "organizations/{$this->organization->id}";

        if (Storage::disk('public')->exists($directory)) {
            $files = Storage::disk('public')->files($directory);

            foreach ($files as $file) {
                // More robust filename matching
                if (preg_match('/^logo\.(jpg|jpeg|png|webp)$/i', basename($file))) {
                    Storage::disk('public')->delete($file);
                }
            }
        }
    }

    protected function deleteExistingBackground()
    {
        // Use the 'public' disk explicitly
        $directory = "organizations/{$this->organization->id}";

        if (Storage::disk('public')->exists($directory)) {
            $files = Storage::disk('public')->files($directory);

            foreach ($files as $file) {
                // More robust filename matching
                if (preg_match('/^background\.(jpg|jpeg|png|webp)$/i', basename($file))) {
                    Storage::disk('public')->delete($file);
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.organizations.upload-media');
    }
}
