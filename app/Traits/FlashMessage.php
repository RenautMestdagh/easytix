<?php

namespace App\Traits;

trait FlashMessage
{
    protected function flashMessage($message, $type = 'success')
    {
        session()->flash('message', __($message));
        session()->flash('message_type', $type);
        $this->dispatch('flash-message');
    }
}
