<?php

namespace App\Traits;

use App\Models\Venue;
use Illuminate\Support\Facades\Storage;

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

    protected function saveMedia($uploadedFile, $eventId)
    {
        $destinationDirectory = "events/{$eventId}";
        $destinationPath = Storage::disk('public')->path($destinationDirectory);
        if (!file_exists($destinationPath))
            Storage::disk('public')->makeDirectory($destinationDirectory);

        $destinationFile = "events/{$eventId}/{$uploadedFile['name']}";
        $counter = 1;

        // Check if file exists and add suffix if needed
        $pathInfo = pathinfo($uploadedFile['name']);
        while (Storage::disk('public')->exists($destinationFile)) {
            $uploadedFile['name'] = "{$pathInfo['filename']}_$counter.{$pathInfo['extension']}";
            $destinationFile = "events/{$eventId}/{$uploadedFile['name']}";
            $counter++;
        }

        $succeeded = rename(
            $uploadedFile['path'],
            Storage::disk('public')->path($destinationFile)
        );
        if($succeeded)
            return $uploadedFile['name'];
        return null;
    }
}
