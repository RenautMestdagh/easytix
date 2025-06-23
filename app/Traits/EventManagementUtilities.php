<?php

namespace App\Traits;

use App\Models\Venue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait EventManagementUtilities
{
    use FlashMessage;

    protected function determinePublishStatus()
    {
        return match ($this->publish_option) {
            'publish_now' => ['is_published' => true, 'publish_at' => null],
            'schedule' => ['is_published' => false, 'publish_at' => $this->publish_at],
            'unlisted' => ['is_published' => false, 'publish_at' => null],
            default => ['is_published' => false, 'publish_at' => null],
        };
    }

    public function venueSelected($venueId, $venueName)
    {
        $venue = Venue::find($venueId);
        if(!$venue)
            $this->use_venue_capacity = false;
        else if(empty($this->max_capacity))
            $this->use_venue_capacity = true;

        $this->venue_id = $venueId;
    }

    protected function saveMedia($mediaType, $eventId)
    {
        $storedPath = Storage::disk('public')->putFile(
            "events/{$eventId}",
            $this->$mediaType
        );

        return Str::afterLast($storedPath, '/');
    }

    /**
     * Remove an image from storage and update the event
     */
    public function removeUpload($event, string $fieldName, ?string $successMessage = null)
    {
        try {
            // Delete the file from storage if it exists
            if ($event->{$fieldName})
                Storage::disk('public')->delete("events/{$event->id}/{$event->{$fieldName}}");

            if ($successMessage)
                $this->flashMessage($successMessage);
            return true;
        } catch (\Exception $e) {
            Log::error('Error deleting image: ' . $e->getMessage());
            $this->flashMessage('Error while deleting image.', 'error');
            return false;
        }
    }

    public function handleFileRemoval($dbField)
    {
        $inputVar = $dbField . 'Input';
        $this->$inputVar = [];
    }
}
