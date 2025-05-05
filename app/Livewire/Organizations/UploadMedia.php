<?php

namespace App\Livewire\Organizations;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Organization;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\OrganizationMediaRequest;
use Illuminate\Support\Str;

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

        $updated = false;

        if ($this->logo) {
            $this->uploadLogo();
            $updated = true;
        }

        if ($this->background) {
            $this->uploadBackground();
            $updated = true;
        }

        if ($this->favicon) {
            $this->uploadFavicon();
            $updated = true;
        }

        if ($updated) {
            $this->favicon = $this->logo = $this->background = null;
            session()->flash('message', __('Media updated successfully.'));
            $this->dispatch('flash-message');
        }

    }

    protected function validateRequest($field = null)
    {
        $request = new OrganizationMediaRequest();

        if ($field) {
            $this->validate(
                [$field => $request->rules()[$field]],
                $request->messages(),
                $request->attributes()
            );
        } else {
            $this->validate(
                $request->rules(),
                $request->messages(),
                $request->attributes()
            );
        }
    }

    protected function uploadFavicon()
    {
        $this->deleteExistingFavicon();

        $filename = $this->generateUniqueFilename('favicon', $this->favicon->extension());

        $this->favicon->storeAs(
            "organizations/{$this->organization->id}",
            $filename,
            'public'
        );

        $this->organization->update(['favicon' => $filename]);
    }

    protected function uploadLogo()
    {
        $this->deleteExistingLogo();

        $filename = $this->generateUniqueFilename('logo', $this->logo->extension());

        $this->logo->storeAs(
            "organizations/{$this->organization->id}",
            $filename,
            'public'
        );

        $this->organization->update(['logo' => $filename]);
    }

    protected function uploadBackground()
    {
        $this->deleteExistingBackground();

        $filename = $this->generateUniqueFilename('background', $this->background->extension());

        $this->background->storeAs(
            "organizations/{$this->organization->id}",
            $filename,
            'public'
        );

        $this->organization->update(['background_image' => $filename]);
    }

    protected function generateUniqueFilename($prefix, $extension)
    {
        $filename = "{$prefix}.{$extension}";

        // If file exists, append a random string
        if (Storage::disk('public')->exists("organizations/{$this->organization->id}/{$filename}")) {
            $random = Str::random(8);
            $filename = "{$prefix}_{$random}.{$extension}";
        }

        return $filename;
    }

    public function removeFavicon()
    {
        $this->deleteExistingFavicon();
        $this->organization->update(['favicon' => null]);
        $this->dispatch('notify',
            type: 'success',
            content: 'Favicon removed successfully'
        );
    }

    public function removeLogo()
    {
        $this->deleteExistingLogo();
        $this->organization->update(['logo' => null]);
        $this->dispatch('notify',
            type: 'success',
            content: 'Logo removed successfully'
        );
    }

    public function removeBackground()
    {
        $this->deleteExistingBackground();
        $this->organization->update(['background_image' => null]);
        $this->dispatch('notify',
            type: 'success',
            content: 'Background removed successfully'
        );
    }

    protected function deleteExistingFavicon()
    {
        if ($this->organization->favicon) {
            Storage::disk('public')->delete(
                "organizations/{$this->organization->id}/{$this->organization->favicon}"
            );
        }
    }

    protected function deleteExistingLogo()
    {
        if ($this->organization->logo) {
            Storage::disk('public')->delete(
                "organizations/{$this->organization->id}/{$this->organization->logo}"
            );
        }
    }

    protected function deleteExistingBackground()
    {
        if ($this->organization->background_image) {
            Storage::disk('public')->delete(
                "organizations/{$this->organization->id}/{$this->organization->background_image}"
            );
        }
    }

    public function render()
    {
        return view('livewire.organizations.upload-media');
    }
}
