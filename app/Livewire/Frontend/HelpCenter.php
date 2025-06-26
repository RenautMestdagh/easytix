<?php

namespace App\Livewire\Frontend;

use App\Mail\SupportMessageMail;
use App\Mail\TicketRecoveryMail;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class HelpCenter extends Component
{
    public $email;
    public $recoverMessage;
    public $supportMessage;

    // Add new properties for support message form
    public $name;
    public $contactEmail;
    public $subject = 'General Inquiry';
    public $message;

    protected $rules = [
        'email' => 'required|email',
        'name' => 'required|string|min:2',
        'contactEmail' => 'required|email',
        'subject' => 'required|string',
        'message' => 'required|string|min:10',
    ];

    public function recoverTickets()
    {
        $this->validateOnly('email');

        // Find all orders with upcoming events for this email
        $orders = Order::whereHas('customer', function($query) {
            $query->where('email', $this->email);
        })
            ->whereHas('event', function($query) {
                $query->where('date', '>=', now()->startOfDay());
            })
            ->with(['event', 'tickets', 'customer'])
            ->get();

        if (!$orders->isEmpty()) {
            try {
                Mail::to($this->email)->queue(new TicketRecoveryMail($orders));
            } catch (\Exception $e) {
                $this->recoverMessage = 'Failed to send your message. Please try again later.';
                Log::error("Failed to send ticket recovery message: {$e->getMessage()}");
                return;
            }
        }

        $this->recoverMessage = 'If you have orders for upcoming events linked to this email, we’ve sent you a summary. Check your inbox (and spam folder) for details.';
        $this->reset('email');
    }

    public function sendSupportMessage()
    {
        $this->validate([
            'name' => 'required|string|min:2',
            'contactEmail' => 'required|email',
            'subject' => 'required|string',
            'message' => 'required|string|min:10',
        ]);

        try {
            Mail::to(config('mail.from.address'))->queue(
                new SupportMessageMail(
                $this->name,
                $this->contactEmail,
                $this->subject,
                $this->message
                ),
            );

            $this->supportMessage = 'Your message has been sent successfully! Our support team will get back to you soon.';
            $this->reset(['name', 'contactEmail', 'subject', 'message']);
        } catch (\Exception $e) {
            $this->supportMessage = 'Failed to send your message. Please try again later.';
            Log::error("Failed to send support message: {$e->getMessage()}");
        }
    }

    public function render()
    {
        return view('livewire.frontend.help-center')
            ->layout('components.layouts.home');
    }
}
