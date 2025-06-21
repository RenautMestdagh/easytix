<?php

namespace App\Traits;

trait TicketTypeManagementUtilities
{
    protected function determinePublishStatus()
    {
        if($this->publish_option === 'with_event' && $this->event->is_published)
            $this->publish_option = 'publish_now';

        return match ($this->publish_option) {
            'publish_now' => ['is_published' => true, 'publish_at' => null, 'publish_with_event' => false],
            'schedule' => ['is_published' => false, 'publish_at' => $this->publish_at, 'publish_with_event' => false],
            'with_event' => ['is_published' => false, 'publish_at' => null, 'publish_with_event' => true],
            'draft' => ['is_published' => false, 'publish_at' => null, 'publish_with_event' => false],
            default => ['is_published' => false, 'publish_at' => null, 'publish_with_event' => false],
        };
    }
}
