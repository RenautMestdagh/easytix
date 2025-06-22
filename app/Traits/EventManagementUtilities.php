<?php

namespace App\Traits;

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

    /**
     * Universal method for uploading images
     *
     * @param \App\Models\Event $event The event model
     * @param string $fieldName The name of the image field (e.g., 'event_image', 'header_image')
     * @param string $prefix Optional prefix for the filename (defaults to field name)
     */
    protected function uploadImage($event, string $fieldName, string $prefix = null)
    {
        if ($event->{$fieldName}) {
            Storage::disk('public')->delete("events/$event->id/$event->$fieldName");
        }
        $prefix = $prefix ?? $fieldName;
        $file = $this->{$fieldName};

        $filename = $this->generateUniqueFilename(
            $prefix,
            $file->extension(),
            $event->id
        );

        $file->storeAs(
            "events/$event->id",
            $filename,
            'public'
        );

        $event->update([$fieldName => $filename]);
    }

    protected function generateUniqueFilename($prefix, $extension, $eventId)
    {
        $filename = "{$prefix}.{$extension}";

        // If file exists, append a random string
        if (Storage::disk('public')->exists("events/{$eventId}/{$filename}")) {
            $random = Str::random(8);
            $filename = "{$prefix}_{$random}.{$extension}";
        }

        return $filename;
    }

    /**
     * Remove an image from storage and update the event
     */
    public function removeImage($event, string $fieldName, string $successMessage)
    {
        try {
            // Delete the file from storage if it exists
            if ($event->{$fieldName})
                Storage::disk('public')->delete("events/{$event->id}/{$event->{$fieldName}}");

            $event->update([$fieldName => null]); // Update field in db
            $this->reset($fieldName); // Reset the file input

            $this->flashMessage($successMessage);
        } catch (\Exception $e) {
            Log::error('Error deleting image: ' . $e->getMessage());
            $this->flashMessage('Error while deleting image.', 'error');
        }
    }
}
