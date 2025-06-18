<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $event;
    public $quantities;
    public $orderTotal;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->event = $order->event; // Assuming all tickets are for same event
        $this->quantities = $this->event->ticketTypes->mapWithKeys(function ($ticket) {
            return [
                $ticket->id => (object) [
                    'name' => $ticket->name,
                    'price_cents' => $ticket->price_cents,
                    'amount' => $this->order->tickets
                        ->where('ticket_type_id', $ticket->id)
                        ->count()
                ]
            ];
        })->all();

        $this->orderTotal = collect($this->quantities)->sum(function($ticket) {
            return $ticket->price_cents * $ticket->amount;
        });
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Order confirmation for {$this->order->event->name} - {$this->event->location}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.order-confirmation',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
