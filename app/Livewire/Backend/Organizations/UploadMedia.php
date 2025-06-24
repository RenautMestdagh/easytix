<?php

namespace App\Livewire\Backend\Organizations;

use App\Http\Requests\OrganizationMediaRequest;
use App\Models\Organization;
use App\Traits\FlashMessage;
use Illuminate\Http\File;
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
    public $faviconInput;
    public $logoInput;
    public $backgroundInput;

    public function mount()
    {
        $this->organization = Organization::findOrFail(session('organization_id'));
    }

    public function uploadMedia()
    {
        $this->favicon = $this->faviconInput ? new File($this->faviconInput[0]['path']) : null;
        $this->logo = $this->logoInput ? new File($this->logoInput[0]['path']) : null;
        $this->background = $this->backgroundInput ? new File($this->backgroundInput[0]['path']) : null;

        $this->validate(
            (new OrganizationMediaRequest())->rules(),
            (new OrganizationMediaRequest())->messages(),
        );

        $updated = false;

        try {
            foreach (['favicon', 'logo', 'background'] as $mediaType) {
                $inputField = $mediaType . 'Input';

                if ($this->$inputField) {
                    $this->saveMedia($mediaType);
                    $this->$inputField = null;
                    $this->$mediaType = null;
                    $updated = true;
                }
            }

            if ($updated) {
                $this->flashMessage('Media updated successfully.');
            }
        } catch (\Exception $exception) {
            Log::error('An error occurred while saving media: ' . $exception->getMessage());
            $this->flashMessage('An error occurred while saving media.', 'error');
        }
    }

    protected function saveMedia($mediaType)
    {
        $this->deleteExistingMedia($mediaType);

        $dbField = $mediaType === 'background' ? 'background_image' : $mediaType;

        $storedPath = Storage::disk('public')->putFile(
            "organizations/{$this->organization->id}",
            $this->$mediaType
        );

        $this->organization->update([$dbField => Str::afterLast($storedPath, '/')]);
    }

    public function removeMedia($mediaType)
    {
        try {
            $this->deleteExistingMedia($mediaType);

            $dbField = $mediaType === 'background' ? 'background_image' : $mediaType;
            $this->organization->update([$dbField => null]);

            $this->flashMessage(ucfirst($mediaType) . ' removed successfully.');
        } catch (\Exception $exception) {
            Log::error("An error occurred while removing {$mediaType}: " . $exception->getMessage());
            $this->flashMessage("An error occurred while removing {$mediaType}.", 'error');
        }
    }

    protected function deleteExistingMedia($mediaType)
    {
        $dbField = $mediaType === 'background' ? 'background_image' : $mediaType;

        if ($this->organization->$dbField) {
            Storage::disk('public')->delete(
                "organizations/{$this->organization->id}/{$this->organization->$dbField}"
            );
        }
    }

    public function render()
    {
        return view('livewire.backend.organizations.upload-media');
    }
}
