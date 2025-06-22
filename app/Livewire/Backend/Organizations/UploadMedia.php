<?php

namespace App\Livewire\Backend\Organizations;

use App\Http\Requests\OrganizationMediaRequest;
use App\Models\Organization;
use App\Traits\FlashMessage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class UploadMedia extends Component
{
    use WithFileUploads, FlashMessage;

    public Organization $organization;
    public $favicon;
    public $logo;
    public $background;

    public function mount(Organization $organization)
    {
        $this->organization = $organization;
    }

    public function updated($propertyName)
    {
        try {
            $this->validateOnly(
                $propertyName,
                (new OrganizationMediaRequest())->rules(),
                (new OrganizationMediaRequest())->messages(),
            );
        } catch (\Exception $exception) {
            $this->$propertyName = null;
            $this->setErrorBag([$propertyName => $exception->validator->getMessageBag()->toArray()[$propertyName][0]]);
        }
    }

    public function save()
    {
        $this->validate(
            (new OrganizationMediaRequest())->rules(),
            (new OrganizationMediaRequest())->messages(),
        );

        $updated = false;

        try {
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
                $this->flashMessage('Media updated successfully.');
            }
        } catch (\Exception $exception) {
            Log::error('An error occurred while saving media: ' . $exception->getMessage());
            $this->flashMessage('An error occurred while saving media.', 'error');
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
        try {
            $this->deleteExistingFavicon();
            $this->organization->update(['favicon' => null]);
            $this->flashMessage('Favicon removed successfully.');
        } catch (\Exception $exception) {
            Log::error('An error occurred while removing favicon: ' . $exception->getMessage());
            $this->flashMessage('An error occurred while removing favicon.', 'error');
        }
    }

    public function removeLogo()
    {
        try {
            $this->deleteExistingLogo();
            $this->organization->update(['logo' => null]);
            $this->flashMessage('Logo removed successfully.');
        } catch (\Exception $exception) {
            Log::error('An error occurred while removing logo: ' . $exception->getMessage());
            $this->flashMessage('An error occurred while removing logo.', 'error');
        }
    }

    public function removeBackground()
    {
        try {
            $this->deleteExistingBackground();
            $this->organization->update(['background_image' => null]);
            $this->flashMessage('Background image removed successfully.');
        } catch (\Exception $exception) {
            Log::error('An error occurred while removing background image: ' . $exception->getMessage());
            $this->flashMessage('An error occurred while removing background image.', 'error');
        }
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
        return view('livewire.backend.organizations.upload-media');
    }
}
